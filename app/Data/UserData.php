<?php

namespace App\Data;

readonly class UserData
{
    public function __construct(
        public string $idUser,
        public string $username,
        public string $password,
        public string $role,
        public int $rowIndex = 0,
    ) {}

    /**
     * @param array<string, string> $row
     */
    public static function fromSheetRow(array $row, int $rowIndex = 0): self
    {
        return new self(
            idUser: $row['id_user'] ?? '',
            username: $row['username'] ?? '',
            password: $row['password'] ?? '',
            role: $row['role'] ?? 'warga',
            rowIndex: $rowIndex,
        );
    }
}
