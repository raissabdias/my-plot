<?php

namespace Database\Seeders;

use App\Models\BookUser;
use App\Models\GlobalBook;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        # Create Admin
        /*
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('Admin123'),
            ]
        );
        */

        # Register Book Reviews for Existing Books
        $existingBooks = GlobalBook::all();

        # When no books exist, create some real backup books
        if ($existingBooks->isEmpty()) {
            $this->command->info('Nenhum livro encontrado. Criando livros reais de backup...');
            $realIds = ['wrOQLV6xB-wC', '5NomkK4EV68C', 'A-hCj0YmCgEC', 'tcSMCwAAQBAJ', 'ZyTCAlUmulAC'];

            foreach ($realIds as $id) {
                GlobalBook::factory()->create([
                    'google_book_id' => $id,
                    'title' => 'Livro Gerado ' . $id
                ]);
            }
            $existingBooks = GlobalBook::all();
        }

        foreach ($existingBooks as $book) {
            $randomUsersCount = rand(3, 8);

            User::factory($randomUsersCount)->create()->each(function ($user) use ($book) {
                // Cria a interação (BookUser) vinculando User -> Livro Existente
                BookUser::factory()->create([
                    'user_id' => $user->id,
                    'global_book_id' => $book->id,
                    'is_public' => true, 
                    'status' => \App\Enums\BookStatus::READ,
                ]);
            });
        }

        $this->command->info('Done!');
    }
}
