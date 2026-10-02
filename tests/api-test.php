<?php
/**
 * Tests automatisés de l'API (plan de tests, QC-07).
 * Rejoue les scénarios de recette finale et les exigences de sécurité
 * avec de vraies requêtes HTTP, comme le ferait le formateur.
 *
 * Usage (site lancé avec `php -S localhost:8000 -t public`) :
 *   php tests/api-test.php http://localhost:8000
 */

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;

final class HttpClient
{
    private string $cookieFile;
    public string $csrf = '';

    public function __construct(private string $baseUrl)
    {
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'mr-cookies');
    }

    /** @return array{status:int, body:array} */
    public function request(string $method, string $route, array $params = [], ?array $body = null, ?string $csrf = null): array
    {
        $url = $this->baseUrl . '/api.php?' . http_build_query(['route' => $route] + $params);
        $headers = ['Accept: application/json'];
        if ($method !== 'GET') {
            $headers[] = 'X-CSRF-Token: ' . ($csrf ?? $this->csrf);
        }
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
        ]);
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $raw = (string) curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $decoded = json_decode($raw, true) ?? ['raw' => $raw];
        if (isset($decoded['data']['csrfToken'])) {
            $this->csrf = $decoded['data']['csrfToken'];
        }
        return ['status' => $status, 'body' => $decoded];
    }

    public function page(string $query): array
    {
        $curl = curl_init($this->baseUrl . '/index.php' . $query);
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $this->cookieFile, CURLOPT_COOKIEFILE => $this->cookieFile]);
        $html = (string) curl_exec($curl);
        return ['status' => curl_getinfo($curl, CURLINFO_RESPONSE_CODE), 'html' => $html];
    }
}

final class TestRunner
{
    private int $passed = 0;
    private array $failures = [];

    public function check(string $label, bool $ok, string $detail = ''): void
    {
        if ($ok) {
            $this->passed++;
            echo "  ✔ $label\n";
        } else {
            $this->failures[] = $label;
            echo "  ✘ $label" . ($detail !== '' ? " — $detail" : '') . "\n";
        }
    }

    public function section(string $title): void
    {
        echo "\n$title\n";
    }

    public function summary(): int
    {
        echo "\n{$this->passed} test(s) réussi(s), " . count($this->failures) . " échec(s).\n";
        return $this->failures === [] ? 0 : 1;
    }
}

$base = rtrim($argv[1] ?? 'http://localhost:8000', '/');
$t = new TestRunner();
$suffix = substr(bin2hex(random_bytes(4)), 0, 6);
$password = 'Ganache2026x';

// les tests de connexion ne doivent pas être freinés par les exécutions précédentes
Database::getConnection()->exec("DELETE FROM login_attempts WHERE ip_address IN ('127.0.0.1', '::1')");

$visitor = new HttpClient($base);
$alice = new HttpClient($base);
$bob = new HttpClient($base);
foreach ([$visitor, $alice, $bob] as $client) {
    $client->request('GET', 'auth/me');
}

$t->section('Pages et données publiques (scénarios 1, 2, 3)');
foreach (['', '?page=recettes', '?page=a-propos', '?page=contact', '?page=recette&slug=praline-orangette'] as $query) {
    $t->check("page « index.php$query » répond 200", $visitor->page($query)['status'] === 200);
}
$notFound = $visitor->page('?page=recette&slug=inexistante');
$t->check('recette inexistante : page 404 conviviale', $notFound['status'] === 404 && str_contains($notFound['html'], 'Retour à l’accueil'));
$menu = $visitor->request('GET', 'recipes/menu');
$t->check('menu : 5 recettes depuis la base (BE-10)', count($menu['body']['data'] ?? []) >= 5);
$detail = $visitor->request('GET', 'recipe', ['slug' => 'praline-orangette']);
$data = $detail['body']['data'] ?? [];
$t->check('détail : ingrédients, étapes avec photo, temps total calculé (BE-12, BE-14)',
    count($data['ingredients'] ?? []) > 0 && count($data['steps'] ?? []) >= 4
    && ($data['steps'][0]['image'] ?? '') !== '' && $data['totalTime'] === $data['prepTime'] + $data['cookTime']);
$unknown = $visitor->request('GET', 'recipe', ['slug' => "x' OR '1'='1"]);
$t->check('recette inconnue / injection dans le slug : 404 sans détail technique (BE-13)',
    $unknown['status'] === 404 && !str_contains(json_encode($unknown['body']), 'SQL'));

$t->section('Visiteur non connecté (scénario 4)');
$t->check('noter sans être connecté : 401', $visitor->request('POST', 'ratings', [], ['recipe' => 'praline-orangette', 'rating' => 5])['status'] === 401);
$t->check('commenter sans être connecté : 401', $visitor->request('POST', 'comments', [], ['recipe' => 'praline-orangette', 'message' => 'Bonjour'])['status'] === 401);
$t->check('requête sans jeton CSRF : 403 (SEC-03)', $visitor->request('POST', 'auth/login', [], ['email' => 'a@b.be', 'password' => 'x'], 'faux-jeton')['status'] === 403);
$t->check('méthode non prévue : 405', $visitor->request('DELETE', 'recipes')['status'] === 405);

$t->section('Inscription et connexion (scénario 5, BE-01 à BE-04)');
$weak = $alice->request('POST', 'auth/register', [], ['username' => 'a', 'email' => 'pas-un-email', 'password' => 'court', 'password_confirm' => 'autre']);
$t->check('inscription invalide : 422 avec une erreur par champ', $weak['status'] === 422
    && isset($weak['body']['errors']['username'], $weak['body']['errors']['email'], $weak['body']['errors']['password'], $weak['body']['errors']['password_confirm']));
$taken = $alice->request('POST', 'auth/register', [], ['username' => "alice_$suffix", 'email' => 'claire@exemple.be', 'password' => $password, 'password_confirm' => $password]);
$t->check('e-mail déjà utilisé : refusé avec un message clair', $taken['status'] === 422 && isset($taken['body']['errors']['email']));
$created = $alice->request('POST', 'auth/register', [], ['username' => "alice_$suffix", 'email' => "alice_$suffix@exemple.be", 'password' => $password, 'password_confirm' => $password]);
$t->check('inscription valide : 201 et utilisateur connecté', $created['status'] === 201 && ($created['body']['data']['user']['username'] ?? '') === "alice_$suffix");
$hash = Database::getConnection()->query("SELECT password_hash FROM users WHERE username = 'alice_$suffix'")->fetchColumn();
$t->check('mot de passe stocké chiffré, jamais en clair (SEC-04)', is_string($hash) && $hash !== $password && password_verify($password, $hash));

$bob->request('POST', 'auth/register', [], ['username' => "bob_$suffix", 'email' => "bob_$suffix@exemple.be", 'password' => $password, 'password_confirm' => $password]);
$bob->request('POST', 'auth/logout');
$wrong = $bob->request('POST', 'auth/login', [], ['email' => "bob_$suffix@exemple.be", 'password' => 'mauvais-mdp-1']);
$noAccount = $bob->request('POST', 'auth/login', [], ['email' => "personne_$suffix@exemple.be", 'password' => 'mauvais-mdp-1']);
$t->check('identifiants incorrects : même message, que l’e-mail existe ou non (BE-02)',
    $wrong['status'] === 401 && $wrong['body']['message'] === $noAccount['body']['message']);
$injection = $bob->request('POST', 'auth/login', [], ['email' => "' OR '1'='1' -- ", 'password' => "' OR '1'='1"]);
$t->check('injection SQL dans la connexion : sans effet (scénario 14)', $injection['status'] === 401);
$login = $bob->request('POST', 'auth/login', [], ['email' => "bob_$suffix@exemple.be", 'password' => $password]);
$t->check('connexion valide : 200', $login['status'] === 200 && ($login['body']['data']['user']['username'] ?? '') === "bob_$suffix");

$attacker = new HttpClient($base);
$attacker->request('GET', 'auth/me');
for ($i = 0; $i < 5; $i++) {
    $attacker->request('POST', 'auth/login', [], ['email' => "cible_$suffix@exemple.be", 'password' => "essai$i-xxxxx"]);
}
$t->check('6e tentative ratée : connexion freinée, 429 (BE-03)',
    $attacker->request('POST', 'auth/login', [], ['email' => "cible_$suffix@exemple.be", 'password' => 'encore-un-essai'])['status'] === 429);

$t->section('Notes en étoiles (scénarios 6, 7, 8, BE-20 à BE-23)');
$before = $visitor->request('GET', 'recipe', ['slug' => 'praline-cerisette'])['body']['data'];
$first = $alice->request('POST', 'ratings', [], ['recipe' => 'praline-cerisette', 'rating' => 4]);
$t->check('note de 4 : nouvelle moyenne et un vote de plus', $first['status'] === 200 && $first['body']['data']['count'] === $before['ratingCount'] + 1);
$second = $alice->request('POST', 'ratings', [], ['recipe' => 'praline-cerisette', 'rating' => 2]);
$t->check('noter à nouveau remplace la note, sans second vote', $second['body']['data']['count'] === $before['ratingCount'] + 1 && $second['body']['data']['userRating'] === 2);
$t->check('la moyenne a une décimale', is_float($second['body']['data']['average']) || is_int($second['body']['data']['average']));
foreach ([0, 6, '4.5', 'abc', '', null] as $value) {
    $result = $alice->request('POST', 'ratings', [], ['recipe' => 'praline-cerisette', 'rating' => $value]);
    $t->check('note falsifiée ' . json_encode($value) . ' : refusée (422)', $result['status'] === 422);
}
$t->check('note sur une recette inexistante : 404', $alice->request('POST', 'ratings', [], ['recipe' => 'nope', 'rating' => 3])['status'] === 404);
$t->check('la note de l’utilisateur est renvoyée avec le détail', ($alice->request('GET', 'recipe', ['slug' => 'praline-cerisette'])['body']['data']['userRating'] ?? null) === 2);

$t->section('Top 3 de l’accueil (scénario 9, BE-30)');
$top = $visitor->request('GET', 'recipes/top')['body']['data'] ?? [];
$t->check('3 recettes renvoyées', count($top) === 3);
$sorted = true;
for ($i = 1; $i < count($top); $i++) {
    $sorted = $sorted && ($top[$i - 1]['averageRating'] ?? 0) >= ($top[$i]['averageRating'] ?? 0);
}
$t->check('classées par note moyenne décroissante', $sorted);

$t->section('Commentaires (scénarios 10, 11, 12, BE-40 à BE-45)');
$t->check('message trop court (après suppression des espaces) : 422',
    $alice->request('POST', 'comments', [], ['recipe' => 'praline-citronnelle', 'message' => '   a     '])['status'] === 422);
$t->check('message de plus de 500 caractères : 422',
    $alice->request('POST', 'comments', [], ['recipe' => 'praline-citronnelle', 'message' => str_repeat('x', 501)])['status'] === 422);
$payload = '<script>alert("xss")</script> Délicieux !';
$posted = $alice->request('POST', 'comments', [], ['recipe' => 'praline-citronnelle', 'subject' => 'Test', 'message' => $payload]);
$comment = $posted['body']['data']['comment'] ?? [];
$t->check('commentaire ajouté : 201 et commentaire renvoyé (BE-42)', $posted['status'] === 201 && ($comment['message'] ?? '') === $payload);
$listed = $visitor->request('GET', 'comments', ['recipe' => 'praline-citronnelle']);
$t->check('le nouveau commentaire apparaît en tête de liste (plus récent d’abord)', ($listed['body']['data']['items'][0]['id'] ?? null) === ($comment['id'] ?? -1));
$page = $visitor->page('?page=recette&slug=praline-citronnelle');
$t->check('la page ne contient jamais de balise <script> injectée (SEC-02)', !str_contains($page['html'], '<script>alert'));
$t->check('suppression du commentaire d’un autre : 403 (scénario 12)',
    $bob->request('DELETE', 'comments', ['id' => $comment['id'] ?? 0])['status'] === 403);
$t->check('suppression par son auteur : 200', $alice->request('DELETE', 'comments', ['id' => $comment['id'] ?? 0])['status'] === 200);
$paged = $visitor->request('GET', 'comments', ['recipe' => 'praline-orangette', 'offset' => 0])['body']['data'];
$t->check('chargement par tranches de 10 (BE-40)', count($paged['items']) === 10 && $paged['hasMore'] === true);
$t->check('un visiteur ne peut supprimer aucun commentaire', array_filter($paged['items'], fn ($c) => $c['canDelete']) === []);
$posted = [];
for ($i = 0; $i < 5; $i++) {
    $posted[] = $bob->request('POST', 'comments', [], ['recipe' => 'dome-glace-rosalie', 'message' => "Commentaire de test n°$i"])['body']['data']['comment']['id'] ?? 0;
}
$t->check('limitation : 6e commentaire en 10 minutes refusé (BE-44)',
    $bob->request('POST', 'comments', [], ['recipe' => 'dome-glace-rosalie', 'message' => 'Encore un'])['status'] === 429);
foreach ($posted as $id) {
    $bob->request('DELETE', 'comments', ['id' => $id]);
}

$t->section('Contact (scénario 13, BE-50)');
$contact = new HttpClient($base);
$contact->page('?page=contact');
$contact->request('GET', 'auth/me');
$invalid = $contact->request('POST', 'contact', [], ['name' => '', 'email' => 'faux', 'subject' => 'a', 'message' => 'court']);
$t->check('message invalide : 422 avec erreurs par champ', $invalid['status'] === 422 && count($invalid['body']['errors']) === 4);
$count = fn () => (int) Database::getConnection()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$beforeCount = $count();
sleep(3);
$valid = $contact->request('POST', 'contact', [], ['name' => 'Testeur', 'email' => 'test@exemple.be', 'subject' => 'Question', 'message' => 'Bonjour, ceci est un message de test.', 'website' => '']);
$t->check('message valide : enregistré et acquitté (201)', $valid['status'] === 201 && $count() === $beforeCount + 1);
$robot = $contact->request('POST', 'contact', [], ['name' => 'Robot', 'email' => 'bot@spam.be', 'subject' => 'Promo', 'message' => 'Achetez nos produits !!!', 'website' => 'http://spam.example']);
$t->check('robot (champ piège rempli) : réponse identique mais rien n’est enregistré', $robot['status'] === 201 && $count() === $beforeCount + 1);
Database::getConnection()->exec("DELETE FROM contact_messages WHERE email = 'test@exemple.be'");

$t->section('Déconnexion (SEC-05)');
$bob->request('POST', 'auth/logout');
$t->check('après déconnexion, plus aucune action réservée', $bob->request('POST', 'ratings', [], ['recipe' => 'praline-orangette', 'rating' => 5])['status'] === 401);

// nettoyage des comptes de test
Database::getConnection()->exec("DELETE FROM users WHERE username IN ('alice_$suffix', 'bob_$suffix')");

exit($t->summary());
