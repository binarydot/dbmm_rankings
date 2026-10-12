<?= $this->extend('layout') ?>

<?php
$labels = [
    'english' => [
        'title' => 'Top Dog Handicap',
        'background' => 'Background',
        'backgroundText' => 'The Top Dog handicap tournament system was developed by Dean Bedlington of Olympian Games in Australia for DBM circa 2000 and later supported in David Young’s Glicko ratings for DBM. It provides a way to run a parallel tournament at a wargames event, awarding prizes based on each player-and-army combination playing against its historical competition record. It also aims to encourage army diversity by penalising commonly used armies and rewarding those that have seen little use.',
        'source' => 'These notes are based on David Young’s notes on the Top Dog system hosted on the',
        'nasamw' => 'NASAMW Glicko page',
        'handicapBasis' => 'The Top Dog handicap is not based on Glicko ratings, but uses the data collected for them. Handicaps are based on average score per game, calculated on a 0-to-25 basis for DBMM. This site publishes adjusted Top Dog values for each army and player.',
        'running' => 'Running a Top Dog Tournament',
        'runningText' => 'Run a normal competition with its usual scores and prizes. In addition, award the Top Dog prize to the player with the highest normal score minus their Top Dog handicap multiplied by the number of competition rounds. Normally, players who finish on the normal podium are excluded from winning the Top Dog event.',
        'combined' => 'The total Top Dog handicap per round is two-thirds of the player Top Dog value plus one-third of the army Top Dog value.',
        'missed' => 'If a player misses a round or receives a bye, that round’s score does not count for Top Dog.',
        'details' => 'Details',
        'playerIntro' => 'The player handicap is generally based on the player’s historical average points per game (P), with a discount for newer players:',
        'playerGames' => 'Rated games',
        'playerHandicap' => 'Player handicap',
        'playerZero' => '0',
        'playerOneToSeven' => '1 to 7',
        'playerEightToFifteen' => '8 to 15',
        'playerSixteenPlus' => '16 or more',
        'playerZeroValue' => '1.00',
        'playerOneToSevenValue' => 'max(1.00, P × 0.3333)',
        'playerEightToFifteenValue' => 'max(1.00, P × 0.6667)',
        'playerSixteenPlusValue' => 'P',
        'armyIntro' => 'The army handicap is based on the army’s average score per game (A), with discounts for rarely used armies and penalties for heavily used armies:',
        'armyGames' => 'Games for army',
        'armyHandicap' => 'Army handicap',
        'armyZero' => '0',
        'armyOneToSeven' => '1 to 7',
        'armyEightToFifteen' => '8 to 15',
        'armySixteenToTwentyThree' => '16 to 23',
        'armyTwentyFourToNinetyNine' => '24 to 99',
        'armyHundredToHundredNinetyNine' => '100 to 199',
        'armyTwoHundredToTwoNinetyNine' => '200 to 299',
        'armyThreeHundredPlus' => '300 or more',
        'armyZeroValue' => '0.00',
        'armyOneToSevenValue' => 'A × 0.25',
        'armyEightToFifteenValue' => 'A × 0.50',
        'armySixteenToTwentyThreeValue' => 'A × 0.75',
        'armyTwentyFourToNinetyNineValue' => 'A',
        'armyHundredToHundredNinetyNineValue' => 'A + 2',
        'armyTwoHundredToTwoNinetyNineValue' => 'A + 4',
        'armyThreeHundredPlusValue' => 'A + 2 for each additional 100 games',
        'penaltyNote' => 'Penalties above 99 games were doubled to account for the change from 0-to-10 DBM scoring to 0-to-25 DBMM scoring.',
        'example' => 'Example',
        'exampleText' => 'A player with 12 rated games and an average score of 4.50 who chooses an unused army has a player handicap of 4.50 × 0.6667 = 3.00 and an army handicap of 0.00. The combined per-round handicap is ⅔ × 3.00 + ⅓ × 0.00 = 2.00.',
        'published' => 'The Top Dog values published on this site include the adjustments for the number of games played.',
        'statistics' => 'Army Statistics',
        'armyDirectory' => 'Army Directory',
    ],
    'italian' => [
        'title' => 'Handicap Top Dog',
        'background' => 'Contesto',
        'backgroundText' => 'Il sistema di handicap Top Dog permette di affiancare un torneo basato sui risultati storici dei giocatori e degli eserciti, incoraggiando la varietà nell’uso degli eserciti.',
        'source' => 'Queste note si basano sulle informazioni di David Young sul sistema Top Dog disponibili nella',
        'nasamw' => 'pagina Glicko NASAMW',
        'handicapBasis' => 'L’handicap Top Dog non si basa sui punteggi Glicko, ma sui punteggi medi per partita convertiti alla scala da 0 a 25. Questo sito pubblica valori Top Dog corretti per ogni esercito e giocatore.',
        'running' => 'Organizzare un torneo Top Dog',
        'runningText' => 'Si svolge un normale torneo e si assegna inoltre il premio Top Dog al giocatore con il punteggio normale più alto dopo aver sottratto l’handicap Top Dog moltiplicato per il numero di turni.',
        'combined' => 'L’handicap Top Dog per turno è composto per due terzi dal valore del giocatore e per un terzo dal valore dell’esercito.',
        'missed' => 'I turni saltati o assegnati come bye non contribuiscono al punteggio Top Dog.',
        'details' => 'Dettagli',
        'playerIntro' => 'L’handicap del giocatore dipende dalla media storica per partita (P), con uno sconto per i nuovi giocatori:',
        'playerGames' => 'Partite classificate',
        'playerHandicap' => 'Handicap giocatore',
        'playerZero' => '0',
        'playerOneToSeven' => 'Da 1 a 7',
        'playerEightToFifteen' => 'Da 8 a 15',
        'playerSixteenPlus' => '16 o più',
        'playerZeroValue' => '1,00',
        'playerOneToSevenValue' => 'max(1,00, P × 0,3333)',
        'playerEightToFifteenValue' => 'max(1,00, P × 0,6667)',
        'playerSixteenPlusValue' => 'P',
        'armyIntro' => 'L’handicap dell’esercito si basa sulla media per partita (A), con sconti per gli eserciti poco utilizzati e penalità per quelli molto utilizzati:',
        'armyGames' => 'Partite esercito',
        'armyHandicap' => 'Handicap esercito',
        'armyZero' => '0',
        'armyOneToSeven' => 'Da 1 a 7',
        'armyEightToFifteen' => 'Da 8 a 15',
        'armySixteenToTwentyThree' => 'Da 16 a 23',
        'armyTwentyFourToNinetyNine' => 'Da 24 a 99',
        'armyHundredToHundredNinetyNine' => 'Da 100 a 199',
        'armyTwoHundredToTwoNinetyNine' => 'Da 200 a 299',
        'armyThreeHundredPlus' => '300 o più',
        'armyZeroValue' => '0,00',
        'armyOneToSevenValue' => 'A × 0,25',
        'armyEightToFifteenValue' => 'A × 0,50',
        'armySixteenToTwentyThreeValue' => 'A × 0,75',
        'armyTwentyFourToNinetyNineValue' => 'A',
        'armyHundredToHundredNinetyNineValue' => 'A + 2',
        'armyTwoHundredToTwoNinetyNineValue' => 'A + 4',
        'armyThreeHundredPlusValue' => 'A + 2 ogni 100 partite aggiuntive',
        'penaltyNote' => 'Le penalità oltre 99 partite sono raddoppiate per adattarsi alla scala DBMM da 0 a 25.',
        'example' => 'Esempio',
        'exampleText' => 'Un giocatore con 12 partite classificate e una media di 4,50 che sceglie un esercito mai utilizzato ha handicap giocatore 3,00, handicap esercito 0,00 e handicap totale per turno 2,00.',
        'published' => 'I valori Top Dog pubblicati su questo sito includono le correzioni basate sul numero di partite.',
        'statistics' => 'Statistiche eserciti',
        'armyDirectory' => 'Elenco eserciti',
    ],
];
$text = $labels[$siteLanguage] ?? $labels['english'];
?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1><?= esc($text['title']) ?></h1>
    <nav class="navbar navbar-default" aria-label="<?= esc($text['statistics'], 'attr') ?>">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="<?= site_url('army/armyStats/' . $viewType . '/' . $monthsBack . '/' . $rankingMethod) ?>"><?= esc($text['statistics']) ?></a></li>
                <li><a href="<?= site_url('army/listArmies') ?>"><?= esc($text['armyDirectory']) ?></a></li>
            </ul>
        </div>
    </nav>

    <h2><?= esc($text['background']) ?></h2>
    <p><?= esc($text['backgroundText']) ?></p>
    <p><?= esc($text['source']) ?> <a href="https://www.nasamw.org/notes_on_top_dog_handicapping.htm"><?= esc($text['nasamw']) ?></a>.</p>
    <p><?= esc($text['handicapBasis']) ?></p>

    <h2><?= esc($text['running']) ?></h2>
    <p><?= esc($text['runningText']) ?></p>
    <p><?= esc($text['combined']) ?></p>
    <p><?= esc($text['missed']) ?></p>

    <h2><?= esc($text['details']) ?></h2>
    <p><?= esc($text['playerIntro']) ?></p>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead><tr><th><?= esc($text['playerGames']) ?></th><th><?= esc($text['playerHandicap']) ?></th></tr></thead>
            <tbody>
                <tr><td><?= esc($text['playerZero']) ?></td><td><?= esc($text['playerZeroValue']) ?></td></tr>
                <tr><td><?= esc($text['playerOneToSeven']) ?></td><td><?= esc($text['playerOneToSevenValue']) ?></td></tr>
                <tr><td><?= esc($text['playerEightToFifteen']) ?></td><td><?= esc($text['playerEightToFifteenValue']) ?></td></tr>
                <tr><td><?= esc($text['playerSixteenPlus']) ?></td><td><?= esc($text['playerSixteenPlusValue']) ?></td></tr>
            </tbody>
        </table>
    </div>

    <p><?= esc($text['armyIntro']) ?></p>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead><tr><th><?= esc($text['armyGames']) ?></th><th><?= esc($text['armyHandicap']) ?></th></tr></thead>
            <tbody>
                <tr><td><?= esc($text['armyZero']) ?></td><td><?= esc($text['armyZeroValue']) ?></td></tr>
                <tr><td><?= esc($text['armyOneToSeven']) ?></td><td><?= esc($text['armyOneToSevenValue']) ?></td></tr>
                <tr><td><?= esc($text['armyEightToFifteen']) ?></td><td><?= esc($text['armyEightToFifteenValue']) ?></td></tr>
                <tr><td><?= esc($text['armySixteenToTwentyThree']) ?></td><td><?= esc($text['armySixteenToTwentyThreeValue']) ?></td></tr>
                <tr><td><?= esc($text['armyTwentyFourToNinetyNine']) ?></td><td><?= esc($text['armyTwentyFourToNinetyNineValue']) ?></td></tr>
                <tr><td><?= esc($text['armyHundredToHundredNinetyNine']) ?></td><td><?= esc($text['armyHundredToHundredNinetyNineValue']) ?></td></tr>
                <tr><td><?= esc($text['armyTwoHundredToTwoNinetyNine']) ?></td><td><?= esc($text['armyTwoHundredToTwoNinetyNineValue']) ?></td></tr>
                <tr><td><?= esc($text['armyThreeHundredPlus']) ?></td><td><?= esc($text['armyThreeHundredPlusValue']) ?></td></tr>
            </tbody>
        </table>
    </div>
    <p><?= esc($text['penaltyNote']) ?></p>

    <h2><?= esc($text['example']) ?></h2>
    <p><?= esc($text['exampleText']) ?></p>
    <p><?= esc($text['published']) ?></p>
</div>
<?= $this->endSection() ?>
