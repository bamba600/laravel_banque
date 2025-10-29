<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin par défaut
        $this->command->info('Création du super admin...');
        User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@banque.com',
            'password' => bcrypt('Admin@123'),
        ]);

        // 2. Admins supplémentaires
        $this->command->info('Création des admins...');
        User::factory()
            ->count(3)
            ->admin()
            ->create();

        // 3. Clients standards
        $this->command->info('Création des clients standards...');
        User::factory()
            ->count(10)
            ->create();

        // 4. Clients Orange Money
        $this->command->info('Création des clients Orange Money...');
        User::factory()
            ->orangeMoney()
            ->count(5)
            ->create();

        // 5. Clients Free Money
        $this->command->info('Création des clients Free Money...');
        User::factory()
            ->freeMoney()
            ->count(5)
            ->create();

        // 6. Clients inactifs
        $this->command->info('Création des clients inactifs...');
        User::factory()
            ->inactif()
            ->count(3)
            ->create();

        // 7. Clients non vérifiés
        $this->command->info('Création des clients non vérifiés...');
        User::factory()
            ->unverified()
            ->count(2)
            ->create();

        // Stats finales
        $this->command->info('Statistiques des utilisateurs créés :');
        $this->command->table(
            ['Type', 'Statut', 'Nombre'],
            [
                ['Admin', 'Actif', User::where('type', User::TYPE_ADMIN)->count()],
                ['Client', 'Actif', User::where('type', User::TYPE_CLIENT)->where('statut', User::STATUT_ACTIF)->count()],
                ['Client', 'Inactif', User::where('type', User::TYPE_CLIENT)->where('statut', User::STATUT_INACTIF)->count()],
                ['Client', 'Non vérifié', User::whereNull('email_verified_at')->count()],
            ]
        );
    }
}
