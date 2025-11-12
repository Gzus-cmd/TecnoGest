<?php

namespace Database\Seeders;

use App\Models\OS;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OSSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $operatingSystems = [
            // Windows - Versiones modernas
            [
                'name' => 'Windows 11',
                'version' => 'Pro 23H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 11',
                'version' => 'Home 23H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 11',
                'version' => 'Enterprise 23H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 11',
                'version' => 'Education 23H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 10',
                'version' => 'Pro 22H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 10',
                'version' => 'Home 22H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 10',
                'version' => 'Enterprise 22H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 10',
                'version' => 'Education 22H2',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 10',
                'version' => 'LTSC 2021',
                'architecture' => '64-bit',
            ],

            // Windows - Versiones anteriores (para equipos antiguos)
            [
                'name' => 'Windows 8.1',
                'version' => 'Pro',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 8.1',
                'version' => 'Enterprise',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 7',
                'version' => 'Professional SP1',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows 7',
                'version' => 'Ultimate SP1',
                'architecture' => '64-bit',
            ],

            // Linux - Distribuciones educativas/empresariales
            [
                'name' => 'Ubuntu',
                'version' => '24.04 LTS (Noble Numbat)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Ubuntu',
                'version' => '22.04 LTS (Jammy Jellyfish)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Ubuntu',
                'version' => '20.04 LTS (Focal Fossa)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Linux Mint',
                'version' => '21.3 (Virginia)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Debian',
                'version' => '12 (Bookworm)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Debian',
                'version' => '11 (Bullseye)',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Fedora Workstation',
                'version' => '39',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'CentOS',
                'version' => '9 Stream',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Rocky Linux',
                'version' => '9.3',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Red Hat Enterprise Linux',
                'version' => '9.3',
                'architecture' => '64-bit',
            ],

            // Linux - Distribuciones ligeras (para equipos antiguos)
            [
                'name' => 'Lubuntu',
                'version' => '24.04 LTS',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Xubuntu',
                'version' => '24.04 LTS',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Linux Lite',
                'version' => '6.6',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Zorin OS',
                'version' => '17 Lite',
                'architecture' => '64-bit',
            ],

            // Linux - Educativas específicas
            [
                'name' => 'Edubuntu',
                'version' => '24.04 LTS',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Huayra Linux',
                'version' => '5.0 (Argentina)',
                'architecture' => '64-bit',
            ],

            // macOS - Versiones recientes (para equipos Mac)
            [
                'name' => 'macOS',
                'version' => 'Sonoma 14',
                'architecture' => 'ARM64 (Apple Silicon)',
            ],
            [
                'name' => 'macOS',
                'version' => 'Sonoma 14',
                'architecture' => 'x86_64 (Intel)',
            ],
            [
                'name' => 'macOS',
                'version' => 'Ventura 13',
                'architecture' => 'ARM64 (Apple Silicon)',
            ],
            [
                'name' => 'macOS',
                'version' => 'Ventura 13',
                'architecture' => 'x86_64 (Intel)',
            ],
            [
                'name' => 'macOS',
                'version' => 'Monterey 12',
                'architecture' => 'x86_64 (Intel)',
            ],
            [
                'name' => 'macOS',
                'version' => 'Big Sur 11',
                'architecture' => 'x86_64 (Intel)',
            ],

            // Chrome OS (para Chromebooks educativos)
            [
                'name' => 'Chrome OS',
                'version' => '120',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Chrome OS Flex',
                'version' => '120',
                'architecture' => '64-bit',
            ],

            // Sistemas especializados
            [
                'name' => 'Windows Server',
                'version' => '2022 Standard',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Windows Server',
                'version' => '2019 Standard',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Ubuntu Server',
                'version' => '24.04 LTS',
                'architecture' => '64-bit',
            ],
            [
                'name' => 'Ubuntu Server',
                'version' => '22.04 LTS',
                'architecture' => '64-bit',
            ],
        ];

        foreach ($operatingSystems as $os) {
            OS::create($os);
        }

        $this->command->info('✅ Sistemas Operativos creados correctamente:');
        $this->command->line('   • Windows: 13 versiones (11, 10, 8.1, 7, Server)');
        $this->command->line('   • Linux: 18 distribuciones (Ubuntu, Debian, Fedora, CentOS, etc.)');
        $this->command->line('   • macOS: 6 versiones (Sonoma, Ventura, Monterey, Big Sur)');
        $this->command->line('   • Chrome OS: 2 versiones');
        $this->command->line('   📦 TOTAL: 39 sistemas operativos disponibles');
    }
}
