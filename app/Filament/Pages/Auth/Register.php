<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use App\Models\Team;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register'; 

    public function getFormSchema(): array
    {
        return [
            Wizard::make([
                // PASUL 1 – Email
                Wizard\Step::make('Email')
                    ->icon('heroicon-o-envelope')
                    ->completedIcon('heroicon-s-check-circle')
                    ->schema([
                        Section::make('Bine ai venit la CRMPro')
                            ->description('Creează-ți contul și începe să-ți gestionezi clienții mai eficient')
                            ->aside()
                            ->schema([
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(table: $this->getUserModel(), column: 'email')
                                    ->label('Adresa de email')
                                    ->placeholder('ex: nume@domeniu.ro'),
                            ]),
                    ]),

                // PASUL 2 – Profil complet
                Wizard\Step::make('Profil')
                    ->icon('heroicon-o-user')
                    ->completedIcon('heroicon-s-check-circle')
                    ->schema([
                        Section::make('Completează-ți profilul')
                            ->description('Câteva informații despre tine pentru a personaliza experiența')
                            ->aside()
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('Prenume')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('last_name')
                                    ->label('Nume')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->password()
                                    ->confirmed()
                                    ->required()
                                    ->minLength(8)
                                    ->label('Parola')
                                    ->revealable(),

                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->required()
                                    ->label('Confirmă parola')
                                    ->revealable(),

                                TextInput::make('phone')
                                    ->label('Număr de telefon')
                                    ->tel()
                                    ->placeholder('+407xxxxxxxx'),
                            ]),
                    ]),

                // PASUL 3 – Organizație / Team
                Wizard\Step::make('Organizație')
                    ->icon('heroicon-o-building-office-2')
                    ->completedIcon('heroicon-s-check-circle')
                    ->schema([
                        Section::make('Cum se numește organizația ta?')
                            ->description('Acesta va fi numele workspace-ului tău în CRMPro')
                            ->aside()
                            ->schema([
                                TextInput::make('team_name')
                                    ->label('Numele organizației')
                                    ->required()
                                    ->placeholder('Ex: Compania Mea SRL')
                                    ->helperText('Poți invita alți membri în organizație după ce finalizezi configurarea. Facturarea se face per loc utilizator.'),
                            ]),
                    ]),
            ])
            ->submitAction(view('filament.pages.auth.register.submit')) // butonul custom "Înainte"
            ->skippable(false)
            ->startOnStep(1),
        ];
    }

    protected function handleRegistration(array $data): \Illuminate\Contracts\Auth\Authenticatable
    {
        // Creăm Team-ul (organizația)
        $team = Team::create([
            'name' => $data['team_name'],
        ]);

        // Creăm User-ul
        $user = $this->getUserModel()::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'name'       => $data['first_name'] . ' ' . $data['last_name'], // dacă vrei să păstrezi și câmpul name
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'phone'      => $data['phone'] ?? null,
            'team_id'    => $team->id,
        ]);

        // Opțional: îl facem owner al team-ului (dacă ai roluri)
        // $user->assignRole('owner'); 

        event(new Registered($user));

        // Logăm user-ul automat
        auth()->login($user);

        return $user;
    }
}