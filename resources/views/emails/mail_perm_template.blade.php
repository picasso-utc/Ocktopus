@php
    use \Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Informations de la Perm</title>
</head>
<body>
<p>Yo yo yooooooo q:D</p>

<p>Si vous lisez ce mail, c’est que vous avez franchi la première étape pour devenir de super permanencier.e.s du PIC ! 🎉 Votre perm <strong>{{ $record->nom }}</strong> a été sélectionnée !</p>

<p>Vous avez demandé à l'équipe animation du Pic'Asso de tenir des permanences ce semestre. Petit rappel : le Pic'Asso est un foyer étudiant, ouvert toute la journée, et tenir une perm signifie être présent matin, midi et soir. On a fait de notre mieux pour respecter vos préférences et voici les créneaux qui vous sont attribués :</p>

<h3>Votre journée : </h3>{{ Carbon::parse($creneaux->first()->date)->translatedFormat('d F Y') }}<br>



<h3>Quelques petits rappels pour que tout roule :</h3>

<ol>
    <li><strong>La bouffe, c’est banger :</strong> En vrai y’a pas d’obligation de préparer un repas pour les perms du midi (sauf si c’est un mercredi, jeudi). Maiiiis si vous êtes motivés faites vous plaisir ! Il faudra juste nous dire à l’avance ce que vous voulez qu’on puisse le commander 😉</li>

    <li><strong>Perm du soir, point crucial :</strong> N'oubliez pas de ramener un chèque de caution de 200€ à l’ordre de PVDC PICASSO le jour de votre perm.</li>

    <li><strong>On ne rigole pas avec ça :</strong> La vente d’alcool autre que celui fourni par le Pic'Asso est strictement interdite, sauf autorisation (et c’est rare !). Les bouteilles sont consignées et AUCUNE bière ou Ecocup ne doit sortir de l'enceinte du PICASSO. 🍻🚫</li>

    <li><strong>La ponctualité, c’est la clé :</strong> Votre présence est obligatoire pour le briefing de l'astreinteur ou de l’astreintrice, aux horaires suivants : 9h45 (perm du matin), 12h00 (perm du midi, ou 11h30 si vous préparez un repas (ça dépend du repas évidemment)), et 17h30-18h00 max (perm du soir). Soyez là à l’heure pour installer la déco et recevoir les consignes !</li>

    <li><strong>Comportement exemplaire :</strong> Un permanencier est toujours sobre et respectueux. Tout dérapage sera sévèrement sanctionné. On compte sur vous pour garder le Pic en bon état et préserver l'ambiance. ✌️</li>

    <li><strong>L’heure c’est l’heure :</strong> La vente d’alcool est autorisée de 18h30 à 21h30. À 21h56, on lance la Traviata et l’allumage des lumières, puis on évacue la salle et la terrasse. À 23h, tout doit être nettoyé et tout le monde dehors. Pendant l'évacuation du pic : gardez votre calme et n'oubliez pas le gilet jaune qui vous sera remis par l'astreintrice. Les abords du Pic doivent être dégagés très rapidement (sur le trottoir tout le long et sur le parvis de BF) pour éviter le bruit et ainsi les plaintes des voisins.</li>

    <li><strong>Lâchez-vous sur la déco et les animations ! :</strong> Vous avez un budget de 20€ pour dynamiser notre cher foyer. Que vous soyez une asso ou juste un groupe de potes, éclatez-vous, faites-nous rêver, et n’oubliez pas de mettre la facture à l’ordre de PVDC PICASSO.</li>
    <li><strong>Concours de qualité :</strong> À la fin du semestre, les assos les plus créatives et engagées seront récompensées lors du repas des permanenciers (RDP). Des prix seront remis, alors faites chauffer la déco, les animations et le menu ! 🔥🏆</li>
</ol>
    <h3>En plus, voici quelques conseils pratiques :</h3>

    <ul>
        <li><strong>Sécurité d’abord !:</strong> Toujours mettre 2 personnes en sécu pente et 2 en sécu escaliers tout au long de la soirée (les gens peuvent changer, pas de stress).</li>
        <li><strong>Traviata en vue :</strong> Après la Traviata, ajoutez 3-4 personnes en sécu trottoir, en fonction de l'affluence attendue.</li>
        <li><strong>Perm caisse :</strong> Gardez toujours un permanencier à la caisse pour ramasser les écocups et bouteilles.</li>
        <li><strong>Imprimez et partagez :</strong> Un planning vous a été mis en pièce jointe, celui-ci doit être rempli en tenant compte des remarques ci-dessus. Le planning doit être imprimé en 4 exemplaires (possible avec notre super imprimante !) et n’oubliez pas de le prendre en photo pour bien vous repérer. Partagez-le aussi dans votre groupe !</li>
    </ul>

    <p>N’oubliez pas qu’en cas de doute, vous pouvez demander de l’aide à l’astreinteur ou à l’astreintrice, on est là pour ça.</p>

    <p><strong>Dernière chose ultra importante:</strong> CONFIRMEZ-NOUS par retour de mail que vous avez bien reçu et lu ce message !</p>

    <p>Voilà, c’est tout pour le moment. Si vous avez des questions, des requêtes ou des propositions, foncez et envoyez-nous un petit mail à <a href="mailto:team.anim.picasso@gmail.com">team.anim.picasso@gmail.com</a>.</p>

    <p>Le PIC’ASSO qui vous aime ❤️</p>
</body>
</html>
