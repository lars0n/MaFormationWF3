//alert("WOW ! Tu es sur ma page!")

// Deux slassh pour faire un commentaire uniligne

/*
    Ici, je peux faire un commentaire
    sur plusieur lignes.

    Raccourci : CTRL + /
            ou  CTRL + shift +/

*/


// -- 1 : Déclarer une variable en js
var Prenom;

// -- 2 : Affecter une valeur

Prenom = "Hugo";

// -- 3 : Afficher la variable dans la console

console.log(Prenom);

/*--------------------------------------------
| ~ ~ ~ ~ ~ ~ Les Types de variables ~ ~ ~ ~ |
--------------------------------------------*/

// -- Ici, typeof me permet de connaitre le type de ma variable.
console.log(typeof Prenom);

// -- Déclaration d'un nombre 
var Age = 40;

// -- Afficher dans la console
console.log(Age);

// -- Connaitre son Type
console.log(typeof Age);



                        /* -------------------------------------------------- *\ 
                        |
                        |    Les variables déclarées directement à la racine 
                        |    du fichier JS sont appelées variables GLOBALS.
                        |
                        |    Elles sont disponibles dans l'ensemble de votre
                        |    document, y compris dans les fonctions.
                        |
                        |    ###
                        |
                        |    Les variables déclarées à l'interieur d'une 
                        |    fonction sont appelées variables Locales.
                        |
                        |
                        |    Depuis ECMA 6, vous pouvez déclarer une variable
                        |    avec le mot-clé "let".
                        |
                        |    Votre variable sera alors accessible uniquement
                        |    dans le bloc dans lequel elle est contenu cad
                        |    déclarée.
                        |
                        |    Si elle est déclarée dans une condition, elle 
                        |    sera disponible uniquement dans le bloc de la
                        |    condition.
                        |
                        \* -------------------------------------------------- */

// -- Les Variables Float
var uneDecimale = -2.984;
console.log(uneDecimale);
console.log(typeof uneDecimale);

// -- Les Booléens (VRAI / FAUX)

var unBooleen = false; // -- true
console.log(unBooleen);
console.log(typeof unBooleen);

// -- Les Constantes 

/* La déclaration CONST permet de crér une constante accessible uniquement en lecture.
   sa valeur, ne pourra pas être modifiée par des réaffectations ultérieures.
   une constante ne peut pas être déclarée à nouveau.
*/

// -- Généralement, par convention, les constantes sont en majuscules. 

const HOST = "localhost";
const USER = "root";
const PASSWORD = "mysql";

// -- Je ne peux pas faire cela..
// USER = "hugo";
// Uncaught Typeserror: assignement to constant variable

/* -------------------------------------------------- *\ 
                  La minute info

    Au fur et à mesure que l'on affect ou ré-affecte
    des valeurs à une variable, celle-ci prend la 
    nouvelle valeur et le nouveau type.
    En Javascript (ECMA Script); les varibles sont 
    auto-typées.

    Pour convertir une variable de type NUMBER en
    STRING et STRING en NUMBER je peux utiliser les 
    fonctions native de javascript

\* -------------------------------------------------- */

var unNombre = "24";
console.log(unNombre);
console.log(typeof unNombre);

// -- La fonction parseInt() pour retourner un Entier à partir de ma chaine caractaire
unNombre = parseInt(unNombre);
console.log(unNombre);
console.log(typeof unNombre);

// -- Je ré-affecte une valeur à ma variable
unNombre = "12.55";
console.log(unNombre);
console.log(typeof unNombre);

// -- La Fonction parseFloat() permet de retourner un Float sur la Base d'un STRING
unNombre = parseFloat(unNombre);
console.log(unNombre);
console.log(typeof unNombre);

// -- Conversion d'un Nombre en string toString()
var unNombreEnString = 10;
var maChaineDeCaractere = unNombreEnString.toString();
console.log(unNombreEnString);
console.log(typeof unNombreEnString);
console.log(typeof maChaineDeCaractere);