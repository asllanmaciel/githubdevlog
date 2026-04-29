<?php

namespace App\Support;

use App\Models\WorkspaceInvite;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WorkspaceInviteDelivery
{
    public static function send(WorkspaceInvite $invite): array
    {
        $url = route('workspace.invites.show', $invite->token);
        $workspace = $invite->workspace?->name ?? 'workspace';
        $role = WorkspaceAccess::roles()[$invite->role] ?? $invite->role;

        $message = <<<TEXT
Voce foi convidado para participar do workspace {$workspace} no GitHub DevLog AI.

Papel: {$role}

Acesse o convite:
{$url}

Se voce ainda nao tem conta, crie uma conta usando este mesmo e-mail para aceitar automaticamente o convite.
TEXT;

        try {
            Mail::raw($message, function ($mail) use ($invite, $workspace) {
                $mail->to($invite->email)->subject('Convite para o workspace '.$workspace.' no GitHub DevLog AI');
            });

            $invite->update(['sent_at' => now(), 'delivery_error' => null]);

            return ['sent' => true, 'url' => $url, 'error' => null];
        } catch (Throwable $exception) {
            $invite->update([
                'delivery_error' => app()->isLocal() ? $exception->getMessage() : 'Falha ao enviar convite.',
            ]);

            return ['sent' => false, 'url' => $url, 'error' => $invite->delivery_error];
        }
    }
}