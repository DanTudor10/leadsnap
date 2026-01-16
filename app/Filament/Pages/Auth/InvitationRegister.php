<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Components\Component;
use Filament\Forms\Components\TextInput;

class InvitationRegister extends Register
{
    public ?TeamInvitation $invitation = null;

    public function getMaxWidth(): string
    {
        return '3xl';
    }

    public function mount(): void
    {
        // Nu apela parent::mount() pentru că va încerca să facă verificări de autentificare
        
        // Preia token-ul din parametrul rutei
        $token = request()->route('token');

        if (!$token) {
            abort(404, 'Token invalid');
        }

        $this->invitation = TeamInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->first();

        if (!$this->invitation) {
            abort(404, 'Invitația nu există sau a fost deja acceptată');
        }

        // Pre-completează email-ul
        $this->form->fill([
            'email' => $this->invitation->email,
        ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->email()
            ->required()
            ->label('Adresa de email')
            ->disabled()
            ->dehydrated()
            ->prefixIcon('heroicon-o-envelope')
            ->helperText('Această adresă de email a fost invitată în echipa ' . $this->invitation?->team->name);
    }

    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('first_name')
            ->label('Prenume')
            ->required()
            ->maxLength(255)
            ->prefixIcon('heroicon-o-user')
            ->placeholder('Ion')
            ->autofocus();
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->label('Nume')
            ->required()
            ->maxLength(255)
            ->prefixIcon('heroicon-o-user')
            ->placeholder('Popescu');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->password()
            ->confirmed()
            ->required()
            ->minLength(8)
            ->label('Parola')
            ->revealable()
            ->prefixIcon('heroicon-o-lock-closed')
            ->helperText('Minim 8 caractere');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->password()
            ->required()
            ->label('Confirmă parola')
            ->revealable()
            ->prefixIcon('heroicon-o-lock-closed')
            ->dehydrated(false);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label('Număr de telefon (opțional)')
            ->tel()
            ->placeholder('+407xxxxxxxx')
            ->prefixIcon('heroicon-o-phone');
    }

    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = $this->getUserModel()::create([
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'name'       => $data['first_name'] . ' ' . $data['last_name'],
                'email'      => $this->invitation->email,
                'password'   => Hash::make($data['password']),
                'phone'      => $data['phone'] ?? null,
                'team_id'    => $this->invitation->team_id,
            ]);

            $user->assignRole($this->invitation->role);

            $this->invitation->update([
                'accepted_at' => now(),
            ]);

            // Trimite email de verificare
            $this->sendEmailVerificationNotification($user);

            return $user;
        });
    }

    public function getHeading(): string
    {
        return 'Alătură-te echipei ' . ($this->invitation?->team->name ?? '');
    }

    public function getSubHeading(): ?string
    {
        $roleText = $this->invitation?->role === 'admin' ? 'Administrator' : 'Utilizator';
        return 'Ai fost invitat ca ' . $roleText;
    }
}