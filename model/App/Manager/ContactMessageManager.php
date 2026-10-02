<?php

namespace App\Manager;

use App\Model\ContactMessage;

class ContactMessageManager extends AbstractManager
{
    public function add(ContactMessage $message): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO contact_messages (name, email, subject, message)
             VALUES (:name, :email, :subject, :message)'
        );
        $stmt->execute([
            'name' => $message->getName(),
            'email' => $message->getEmail(),
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
        ]);
        $message->setId((int) $this->pdo->lastInsertId());
    }
}
