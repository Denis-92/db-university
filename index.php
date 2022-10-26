<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interrogazioni via PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php

    define("DB_SERVER", "localhost");
    define("DB_USER", "root");
    define("DB_PASSWORD", "root");
    define("DB_LABEL", "dbuniversity");

    // CONNECTION TO DATABASE
    $conn = new mysqli ( DB_SERVER, DB_USER, DB_PASSWORD, DB_LABEL );

    // MAKE CONDITION TO DETECT CONNECTION-DB ERROR
    $detectErrorDb = $conn && $conn->connect_error;

    // CHECK CONNECTION
    if ( $detectErrorDb ) {
        // CONNECTION ERROR
        echo( "Error: Accesso non riuscito" );
        die();
    } else {
        // CONNECTION OK
        echo ( "link PHP - DB: OK" );
    }

    // RESULT QUERY TEACHER/COURSE
    if ( isset( $_GET["id"] ) ) {

        $idTeacher = $_GET["id"];

        $dbTable = "
            SELECT
                `courses`.`name` AS `courseName`, `courses`.`description`, `courses`.`year`, `courses`.`period`,
                `teachers`.`id`, `teachers`.`surname`, `teachers`.`name` AS `teacherName`, `teachers`.`email`
            FROM `course_teacher`
            INNER JOIN `teachers` ON `teachers`.`id` = `course_teacher`.`teacher_id`
            INNER JOIN `courses` ON `courses`.`id` = `course_teacher`.`course_id`
            WHERE `teacher_id` = ?
        ";

        $stmt = $conn->prepare( $dbTable );
        $stmt->bind_param( "i" , $idTeacher );
        $stmt->execute();

        $outputCourseTeacher = $stmt->get_result();

        // MAKE CONDITION TO CHECK COURSE-TEACHER QUERY RESULT
        $checkCourseTeacher = $outputCourseTeacher && $outputCourseTeacher->num_rows > 0;

        if ( $checkCourseTeacher ) {

            echo ( "<p><a href='index.php'>Home</a></p>" );

            echo ( "<div class='flex flex-column'>" );
            

            while ( $infoTeacher = $outputCourseTeacher->fetch_assoc() ) {
                echo (
                    "<div class='flex flex-center-x' id='course-teacher'>" .
                        "<div><p>" . $infoTeacher["id"] . "</p></div>" .
                        "<div><p>" . $infoTeacher["surname"] . "</p></div>" .
                        "<div><p>" . $infoTeacher["teacherName"] . "</p></div>" .
                        "<div><p>" . $infoTeacher["email"] . "</p></div>" .
                        "<div class='border'><p>Nome corso:<br>" . $infoTeacher["courseName"] . "</p></div>" .
                        "<div class='border'><p>Descrizione corso:<br>" . $infoTeacher["description"] . "</p></div>" .
                        "<div class='border'><p>Anno del corso:<br>" . $infoTeacher["year"] . "</p></div>" .
                        "<div class='border'><p>Semestre del corso:<br>" . $infoTeacher["period"] . "</p></div>" .
                    "</div>"
                );
            }

            echo ( "</div>" );


        } elseif ( $outputCourseTeacher ) {
            // $outputCourseTeacher <= 0
            echo ( "Output: questo insegnante non ha corsi" );
        }

        die();

    }
    // RESULT QUERY TEACHER/COURSE END


    // RESULT QUERY FIRST OUTPUT START
    $dbTable = "
        SELECT `teachers`.`surname`, `teachers`.`name`, `teachers`.`email`, `teachers`.`id`
        FROM `teachers`
        ORDER BY `teachers`.`surname`
        LIMIT 10
    ";

    $outputTeachers = $conn->query($dbTable);

    // MAKE CONDITION TO DETECT QUERY RESULT ERROR
    $detectErrorQuery = $outputTeachers && $outputTeachers->num_rows > 0;

    if( $detectErrorQuery ) {

        echo ( "<div class='flex flex-column'>" );

        while ( $userTeacher = $outputTeachers->fetch_assoc() ) {
            echo (
                "<div class='flex flex-center-x' id='teachers'>" .
                    "<div class='border'><p>" . $userTeacher["surname"] . "</p></div>" .
                    "<div class='border'><p>" . $userTeacher["name"] . "</p></div>" .
                    "<div class='border'><p>" . $userTeacher["email"] . "</p></div>" .
                    "<div class='border'><p> <a href='index.php?id=" . $userTeacher["id"] . "'> Corsi insegnati </a> </p></div>" .
                "</div>"
            );
        }

        echo ( "</div>" );

    } elseif ( $outputTeachers ) {
        // NOT ERROR - $outputTeacher <= 0
        echo ( "Output: non sono stati trovati insegnanti in questa query" );
    } else {
        echo ( "Error: detected query error - index.php row 70" );
        die();
    }
    // RESULT QUERY FIRST OUTPUT END

    ?>
    
</body>
</html>