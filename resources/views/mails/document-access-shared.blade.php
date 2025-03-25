<x-mail::message>
# Document partagé

Le document intitulé <strong>{{ $document->title }}</strong> vient d'être partagé avec vous par l'utilisateur
{{ $document->user->name }}.

Vous y avez désormais accès et pourrez le télécharger à tout moment, tant que votre accès ne sera pas révoqué.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
