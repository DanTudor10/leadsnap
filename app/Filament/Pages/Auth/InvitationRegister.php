<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use App\Models\TeamInvitation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Components\Component;

class InvitationRegister extends Register
{
    public ?TeamInvitation $invitation = null;

    protected static ?string $slug = 'auth/invitation/{token}';

    public function mount(): void
    {
        parent::mount();

        $token = request()->route('token');

        $this->invitation = TeamInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $this->form->fill([
            'email' => $this->invitation->email,
        ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->disabled()
            ->dehydrated();
    }


    protected function handleRegistration(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = $this->getUserModel()::create([
                'name'       => $data['first_name'].' '.$data['last_name'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'email'      => $this->invitation->email,
                'password'   => Hash::make($data['password']),
                'team_id'    => $this->invitation->team_id,
            ]);

            $user->assignRole($this->invitation->role);

            $this->invitation->update([
                'accepted_at' => now(),
            ]);

            $this->sendEmailVerificationNotification($user);

            return $user;
        });
    }
}
