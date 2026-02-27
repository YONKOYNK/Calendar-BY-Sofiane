<?php

    session_start();

    // ici on fait la gestion de cookie pour le user 
    if (isset($_COOKIE['user_id'])) {
        $current_user_id = $_COOKIE['user_id'];
    } else {
        $current_user_id = random_int(100000000, 999999999);
        setcookie('user_id', $current_user_id, time() + 60*60*24*365);
    }

    // je recupere la date 
    if (isset($_GET['month'])) { 
        $month = $_GET['month']; 
    } elseif (isset($_COOKIE['month'])) { 
        $month = $_COOKIE['month']; 
    } else {
        $month = date('n'); 
    }

    if (isset($_GET['year'])) {
        $year = $_GET['year']; 
    }elseif (isset($_COOKIE['year'])) { 
        $year = $_COOKIE['year']; 
    }else {
        $year = date('Y'); 
    }

    // ici je donne donc le cookie pour le mois et laneee et aussi je lui donne une duree de 1ans
    setcookie('month', $month, time()+60*60*24*30);
    setcookie('year', $year, time()+60*60*24*30);

    // je cree les variable de connexion a la base de donee meme si on peut le faire sur la meme ligne new PDO mais cest tjr mieu et plus claire de faire ca comme ca.
    $host = "localhost";
    $dbname = "db_calendar";
    $root = "root";
    $password = "root";

    // je fais la liaison de la base de donnee avec le code php
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $root, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // securiter en plus
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage()); // ici le getmessage sert juste avoir le message derreur au lieu d'avoir un code long avec des chifre ect ect.
    }

    // on fait le traitement du formulaire
    if (isset($_POST['name_event']) && !empty($_POST['name_event']) && isset($_POST['my_date'])) {
        // si on passe la con dition alors on stock les information dans les variable name et date
        $name = $_POST['name_event'];
        $date = $_POST['my_date'];
        
        // ici on check si l'id est present si oui alors on le stock dans la variable idToUpdate et on fait la requete sql sur le $pdo qui lui et la variable qui "STOCK" la bdd
        if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
            $idToUpdate = $_POST['event_id'];
            $stmt = $pdo->prepare("UPDATE events SET name = :name, eventDate = :date WHERE id = :id AND user_id = :uid");
            $stmt->execute(['name' => $name, 'date' => $date, 'id' => $idToUpdate, 'uid' => $current_user_id]);
        } 
        // Sinon c'est un AJOUT
        else {
            $stmt = $pdo->prepare("INSERT INTO events (name, eventDate, user_id) VALUES (:name, :date, :uid)");
            $stmt->execute(['name' => $name, 'date' => $date, 'uid' => $current_user_id]);
        }
        header("Location: index.php");
        exit;
    }

    // ici on check si on appuie sur le lien supprimer et si oui on fait la suppression
    if (isset($_GET['supp'])) {
        $idToDelete = $_GET['supp'];
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $idToDelete, 'uid' => $current_user_id]);
        header("Location: index.php");
        exit;
    }

    // ici on check si on apuie sur modifier et si oui alors ca nous laisse modifier ;
    $edit_event = null;
    if (isset($_GET['edit'])) {
        $idToEdit = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $idToEdit, 'uid' => $current_user_id]);
        $edit_event = $stmt->fetch();
    }

    // Navigation Mois
    if ($month == 1) { 
        $prev_month = 12; 
        $prev_year = $year - 1; 
    }else {
        $prev_month = $month - 1; 
        $prev_year = $year; 
    }

    if ($month == 12) {
        $next_month = 1; 
        $next_year = $year + 1;
    }else {
        $next_month = $month + 1; 
        $next_year = $year; 
    }
?>

<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="style.css?hh">
    <title>Calendar</title>
</head>
<body>
    <div class="wrapp">
        <div class="flex-calendar">
            <div class="month">
                <a href="index.php?month=<?php echo $prev_month ?>&year=<?php echo $prev_year ?>" class="arrow visible"></a>
                <div class="label">
                    <?php echo date('F Y', strtotime($year . '-' . $month . '-01')) ?>
                </div>
                <a href="index.php?month=<?php echo $next_month ?>&year=<?php echo $next_year ?>" class="arrow visible"></a>
            </div>

            <div class="week">
                <div class="day">M</div><div class="day">T</div><div class="day">W</div><div class="day">T</div><div class="day">F</div><div class="day">S</div><div class="day">S</div>
            </div>

            <div class="days">
                <?php
                // VISUEL CALENDRIER (Points de couleur)
                $stmt = $pdo->query("SELECT eventDate FROM events");
                $all_events = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $nb_of_days_in_month = date('t', strtotime($year . '-' . $month . '-01'));
                $first_day_of_month = date('N', strtotime($year . '-' . $month . '-01'));
                $today = date('j');
                
                for ($i = 1; $i < $first_day_of_month; $i++) {
                    echo '<div class="day out"><div class="number"></div></div>';
                }
                    
                for ($i = 1; $i <= $nb_of_days_in_month; $i++) {
                    $classes = 'day';
                    if ($i == $today && $month == date('m')) { $classes .= ' selected'; }
                    $currentDayDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    if (in_array($currentDayDate, $all_events)) { $classes .= ' event'; }
                    echo '<div class="' . $classes . '"><div class="number">' . $i . '</div></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="wrapp add_event">
        <h3>Evénements du mois</h3>
        <ul>
            <?php
                // initialisation des variable month et year
                $m = (int)$month;
                $y = (int)$year;

                // ici je fait une requete simple en sql qui selection tout de la table event oû la date vaut le mois et lanee 
                // et tout cela sera aussi pris par le fetchall qui lui renvoie la data en tableau ce qui va nous permettre de faire une boucle sur le tableau
                $events = $pdo->query("SELECT * FROM events WHERE MONTH(eventDate) = $m AND YEAR(eventDate) = $y ORDER BY eventDate")->fetchAll();

                // si il ya pas devents alors on affiche ca :
                if (!$events) {
                    echo '<li>Aucun événement ce mois-ci.</li>';
                } else { // sinon on fait une boucle sur les events et on fait ca :
                    foreach ($events as $e) {
                        echo '<li>';
                        
                        // on affiche la Date et le Nom que je met bien en gras grace a la balise strong de html
                        echo '<strong>' . $e['eventDate'] . '</strong> : ' . htmlspecialchars($e['name']);
                        if ($e['user_id'] == $current_user_id) {
                            // Lien Modifier
                            echo ' <a href="index.php?edit=' . $e['id'] . '&month=' . $m . '&year=' . $y . '">Modifier</a>';
                            echo ' | ';
                            echo ' <a href="index.php?supp=' . $e['id'] . '&month=' . $m . '&year=' . $y . '" style="color:red;">X</a>';
                        }
                        echo '</li>';
                    }
                }
            ?>
        </ul>
        
        <h3>Ajouter un événement</h3>
        <form method="post" action="index.php">
            <input type="hidden" name="event_id" value="<?php echo ($edit_event) ? $edit_event['id'] : ''; ?>">
            
            <p>
                <label for="my_date">Date</label>
                <input type="date" name="my_date" id="my_date" value="<?php echo ($edit_event) ? $edit_event['eventDate'] : ''; ?>" />
                
                <label for="name_event">Nom Event</label>
                <input type="text" name="name_event" id="name_event" value="<?php echo ($edit_event) ? $edit_event['name'] : ''; ?>"/>
            </p>
            <p>
                <button type="submit">Ajouter</button>
            </p>
        </form>
    </div>

</body>
</html>