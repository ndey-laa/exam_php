document.addEventListener("DOMContentLoaded", () => {
    // fonction pour Plier et déplier une div
    function divPlier(fleche, classPlier, inputcontainer) {
      document.querySelector(`${fleche}`).addEventListener("click", () => {
        document
          .getElementById(`${inputcontainer}`)
          .classList.toggle(`${classPlier}`);
        document.querySelector(`${fleche}`).classList.toggle("rotate");
      });
    }
    // Plier et déplier la div donne personnel
    divPlier("#flecheDonn", "deplier", "pliercontainer1");
    // Plier et déplier la div donne Formation
    divPlier("#flecheform", "deplier", "pliercontainer2");
    // Plier et déplier la div competence
    divPlier("#flecheCOM", "depliercomp", "pliercontainer3");
    // Plier et déplier la div competence
    divPlier("#flecheEXP", "deplier", "pliercontainer4");
    // Plier et déplier la div competence
    divPlier("#flecheLAN", "deplierLAN", "pliercontainer5");
    // Je creer une fonction pour entrer des valeur dans le cv
    function entrervalheadcv(Input, outputCv, container) {
      document.querySelector(`${Input}`).addEventListener("input", () => {
        document.querySelector(`${outputCv}`).innerHTML = document.querySelector(
          `${Input}`
        ).value;
        if (document.querySelector(`${Input}`).value != "") {
          document.querySelector(`${container}`).style.display = "inherit";
        } else {
          document.querySelector(`${container}`).style.display = "none";
        }
      });
    }
    // entrer le nom dans le cv
    entrervalheadcv("#nom", "#nomcv", "#nomcv");
    // entrer le Titre de l'emploi dans le cv
    entrervalheadcv("#jobname", "#job", "#job");
    // entrer l'email dans le cv
    entrervalheadcv("#email", ".mailtxt", ".mailcontainer");
    // entrer l'Telephone dans le cv
    entrervalheadcv("#tel", ".teltxt", ".telcontainer");
    // entrer Address dans le cv
    entrervalheadcv("#addr", ".maptext", ".mapcontainer");
    // entrer Birthday dans le cv
    entrervalheadcv("#birth", ".birthtxt", ".birthcontainer");
    // entrer la nationalité dans le cv
    entrervalheadcv("#Nat", ".nationalitetxt", ".Nationalitecontainer");
  
    // Entre les valeur dans formation
    document.querySelector("#addFormation").addEventListener("click", () => {
      if (
        document.querySelector("#datede").value == "" ||
        document.querySelector("#datefin").value == "" ||
        document.querySelector("#intFormation").value == "" ||
        document.querySelector("#inputscholl").value == "" ||
        document.querySelector("#localité").value == ""
      ) {
        document.querySelector("#eform").innerHTML =
          "Entrer Toutes les valeurs !";
      } else {
        var span1 = document.createElement("span");
        document.querySelector("#conatainform").appendChild(span1);
        span1.classList.add("txtClass");
        span1.innerHTML = `• &nbsp ${document.querySelector("#datede").value} / ${
          document.querySelector("#datefin").value
        } ${document.querySelector("#intFormation").value} `;
        var span2 = document.createElement("span");
        document.querySelector("#conatainform").appendChild(span2);
        span2.classList.add("txtSchool");
        span2.innerHTML = ` &nbsp &nbsp ${
          document.querySelector("#inputscholl").value
        }-${document.querySelector("#localité").value} <br> `;
        document.querySelector("#Formation").style.display = "inherit";
        document.querySelector("#eform").innerHTML = "";
      }
      //  Réinitialiser les champs
  document.getElementById("inputscholl").value = "";
  document.getElementById("intFormation").value = "";
  document.getElementById("datede").value = "";
  document.getElementById("datefin").value = "";
  document.getElementById("localité").value = "";
      
    });
  
    // Entre les valeur dans Experiences
    document.querySelector("#addEXP").addEventListener("click", () => {
      if (
        document.querySelector("#inputposte").value == "" ||
        document.querySelector("#datedeEXP").value == "" ||
        document.querySelector("#inputemplo").value == "" ||
        document.querySelector("#datefinEXP").value == ""
      ) {
        document.querySelector("#eEXP").innerHTML = "Entrer Toutes les valeurs !";
      } else {
        var spanEXP1 = document.createElement("span");
        document.querySelector("#conatainEXP").appendChild(spanEXP1);
        spanEXP1.classList.add("txtClass");
        spanEXP1.innerHTML = `• &nbsp ${
          document.querySelector("#datedeEXP").value
        } / ${document.querySelector("#datefinEXP").value} ${
          document.querySelector("#inputposte").value
        } `;
        var spanEXP2 = document.createElement("span");
        document.querySelector("#conatainEXP").appendChild(spanEXP2);
        spanEXP2.classList.add("txtSchool");
        spanEXP2.innerHTML = ` &nbsp &nbsp ${
          document.querySelector("#inputemplo").value
        }-${document.querySelector("#inputExploc").value} <br> `;
        document.querySelector("#EXP").style.display = "inherit";
        document.querySelector("#eEXP").innerHTML = "";
      }

      //  Réinitialiser les champs
      document.getElementById("inputposte").value = "";
      document.getElementById("inputemplo").value = "";
      document.getElementById("inputExploc").value = "";
      document.getElementById("datedeEXP").value = "";
      document.getElementById("datefinEXP").value = "";

    });
  
    // Entre les valeur dans Competence
    document.querySelector("#addCompetence").addEventListener("click", () => {
      if (document.querySelector("#inputcomp").value == "") {
        document.querySelector("#ecomp").innerHTML =
          "Entrer Toutes les valeurs !";
      } else {
        var spancom = document.createElement("span");
        document.querySelector("#conataincomp").appendChild(spancom);
        spancom.classList.add("textcomp");
        spancom.innerHTML = `• &nbsp  ${
          document.querySelector("#inputcomp").value
        } `;
        document.querySelector("#Competence").style.display = "inherit";
        document.querySelector("#ecomp").innerHTML = "";
      }

      // Réinitialiser
        document.getElementById("inputcomp").value = "";
    }); 

    // Entre les valeur dans Langues
    document.querySelector("#addlangue").addEventListener("click", () => {
      if (
        document.querySelector("#inputlangue").value == "" ||
        document.querySelector("#inputniveau").value == ""
      ) {
        document.querySelector("#elang").innerHTML =
          "Entrer Toutes les valeurs !";
      } else {
        var spanlang = document.createElement("span");
        document.querySelector("#conatainlangue").appendChild(spanlang);
        spanlang.classList.add("textcomp");
        spanlang.innerHTML = `• &nbsp  ${
          document.querySelector("#inputlangue").value
        } : ${document.querySelector("#inputniveau").value}`;
        document.querySelector("#Langues").style.display = "inherit";
        document.querySelector("#elang").innerHTML = "";
      } 

      // Réinitialiser
  document.getElementById("inputlangue").value = "";
  document.getElementById("inputniveau").value = "";
    });
  
    
   });