<x-mail::message>
# Mot de passe oublié

Veuillez utiliser le code suivant pour la réinitialisation de votre mot de passe sur {{ config('app.name') }}.<br>
<strong>{{ $token }}</strong><br>

Attention, il n'est valide que pendant {{ config('app.password_reset_token_lifetime') }} minutes

Si ce n'est pas vous avez lancé la procédure, veuillez ignorer ce mail.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
