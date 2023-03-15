
//On crée un objet XMLHttpRequest
let xhr = new XMLHttpRequest();

//On initialise notre requête avec open()
xhr.open("GET", "une/url");

//On veut une réponse au format JSON
xhr.responseType = "json";

//On envoie la requête
xhr.send();

//Dès que la réponse est reçue...
xhr.onload = function(){
    //Si le statut HTTP n'est pas 200...
    if (xhr.status != 200){ 
        //...On affiche le statut et le message correspondant
        alert("Erreur " + xhr.status + " : " + xhr.statusText);
    //Si le statut HTTP est 200, on affiche le nombre d'octets téléchargés et la réponse
    }else{ 
        alert(xhr.response.length + " octets  téléchargés\n" + JSON.stringify(xhr.response));
    }
};

//Si la requête n'a pas pu aboutir...
xhr.onerror = function(){
    alert("La requête a échoué");
};

//Pendant le téléchargement...
xhr.onprogress = function(event){
    //lengthComputable = booléen; true si la requête a une length calculable
    if (event.lengthComputable){
        //loaded = contient le nombre d'octets téléchargés
        //total = contient le nombre total d'octets à télécharger
        alert(event.loaded + " octets reçus sur un total de " + event.total);
    }
};



