<html>
<head>
<title>My Favorite Bookmarks</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<link type="text/css" rel="stylesheet" href="materialize/css/materialize.min.css"  media="screen,projection"/>
</head>
<body style="background-color: grey;color: white;">
<?php
error_reporting(E_ALL ^ E_NOTICE);
//include the function to clean data
include "cleanData.php";
//create an array for errors
$errors = array();

//start by cleaning the data
$choice = $_REQUEST['choice'];
$choice = cleanData($choice);

//set an array to hold valid choice values
$choice_array = array(
    "Add Bookmark",
    "add",
    "view",
    "delete"
);

//main logic of what to do when
if (in_array($choice, $choice_array)) {
    //print "you selected $choice";
    if ($choice == "Add Bookmark") {
        //clean data before adding to text file
        $description = cleanData($_POST['description']);
        $bookmark    = cleanData($_POST['bookmark']);
        //validate
        validate($description, $bookmark);
    } elseif ($choice == "add")
        addForm($description, $bookmark, $errors);
    elseif ($choice == "view")
        showBookmarks();
    elseif ($choice == "delete") {
        $description = cleanData($_POST['description']);
        deleteBookmark($description);
    }
    else
        displayForm();
} else {
    displayForm();
}
//--------begin functions------------------
function validate($description, $bookmark)
{
    global $errors;
    if ($description == "")
        $errors[0] = "You must fill in a description.";
    if ($bookmark == "")
        $errors[1] = "You must fill in a bookmark. ";
    
    //see if there are errors
    $size = count($errors);
    if ($size > 0)
        addForm($description, $bookmark, $errors);
    else
        addBookmark($description, $bookmark);
} //end validate

function addBookmark($description, $bookmark)
{
    // include header file
    include "header.html";
    //open textfile and write data to file
    $fp = fopen("bookmarks.txt", "a");
    fwrite($fp, "$description\t $bookmark\n");
    print "bookmark added";
    fclose($fp);
    
    //include html footer file
    include "footer.html";
} //end addBookmark

function deleteBookmark($description)
{
    $self = $_SERVER['PHP_SELF'];
    $array = array();
    $i = 0;
    $handle = fopen("bookmarks.txt", "r");

    if($handle) {
    while(($line = fgets($handle)) !== false) {
        if(strpos($line, $description) !== false) {} 
            else {
                $array[$i] = $line;  
                $i++;
        }
    }
    fclose($handle);
    } else {
        echo 'file error';
    }
    $fh = fopen("bookmarks.txt", "w");
    foreach($array as $a) {
        fwrite($fh, $a);
    }
    fclose($fh);
    showBookmarks();
    //include html footer file
    include "footer.html";
} //end deleteBookmark

function addForm($description, $bookmark, $errors)
{
    // include header file
    $self = $_SERVER['PHP_SELF'];
    include "header.html";
    print <<<HERE
<div class="container"><form method="post" action="$self">
<p> Enter a new bookmark to add: </p>
<p>Description: <input name="description" type="text" id="description" value="$description" />$errors[0]
<br />
URL: <input name="bookmark" type="text" id="bookmark" value="$bookmark" />$errors[1]
</p>
<p>
<input class="waves-effect waves-light btn" style="background-color: #424242;" name="choice" type="submit" id="choice" value="Add Bookmark" />
</p>
</form></div>
HERE;
    //include html footer file
    include "footer.html";
} //end addForm

function displayForm()
{
    // include header file
    include "header.html";
    //include html footer file
    include "footer.html";
} //end displayForm

function showBookmarks()
{
    $self = $_SERVER['PHP_SELF'];
    include "header.html";
    @$fp = fopen("bookmarks.txt", "r");
    if (!$fp)
        print "No bookmarks set.";
    else {
        /* print "<table><tr><th width=\"45%\">Description</th>
<th width=\"55%\">URL</th></tr>"; */

        print "<div class=\"container\"><table class=\"bordered\">
    <thead>
        <tr>
            <th width=\"55%\">Description</th>
            <th>Link</th>
            <th>Delete</th>
        </tr>
    </thead>";
        $theData = file("bookmarks.txt");
        
        foreach ($theData as $line) {
            $line = rtrim($line);
            print("<tr>");
            list($description, $bookmark) = explode("\t", $line);
            print("<td>$description</td>");
            print("<td><a href=\"$bookmark\" target=\"_blank\">OPEN</a></td>");
            print("<td><form method=\"post\" action=\"$self?choice=delete\">
                <input name=\"description\" type=\"hidden\" id=\"description\" value=\"$description\" />
                <input class=\"waves-effect waves-light btn\" style=\"background-color: red;\" name=\"choice\" type=\"submit\" id=\"choice\" value=\"delete\"/></form></td></tr>");
        } //end foreach
        print("</table></div>");
    } //end else
    include "footer.html";
} //end showBookmarks
?>
<script type="text/javascript" src="materialize/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="materialize/js/materialize.min.js"></script>
</body>
</html>