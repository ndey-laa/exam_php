document.addEventListener("DOMContentLoaded", () => {
    // LES VARIABLE
    const headCV = document.querySelector("#headCV");
    const CV = document.querySelector("#CV");
    const bodyCV = document.querySelector("#bodyCV");
    const docontainer = document.querySelector("#docontainer");
    const job = document.querySelector("#job");
    var i = 1;
    // CHANGER DE MODEL
    document.querySelector("#Mg").addEventListener("click", () => {
      if (i > 1) {
        i--;
      }
    });
    document.querySelector("#Md").addEventListener("click", () => {
      if (i < 3) {
        i++;
        console.log(i);
      }
      console.log(i);
    });
    document.querySelector(".direction").addEventListener("click", () => {
      if (i == 1) {
        headCV.style.background = "rgb(165, 92, 156)";
        headCV.style.height = "140px";
        headCV.style.width = "100%";
        bodyCV.style.width = "85%";
        docontainer.style.flexDirection = "row";
        docontainer.style.top = "70%";
        job.style.top = "30%";
        CV.style.display = "inherit";
        bodyCV.style.left = "50%";
        bodyCV.style.paddingTop = "0";
      }
      if (i == 2) {
        headCV.style.background = "rgb(62, 131, 171)";
        headCV.style.left = "0";
        docontainer.style.flexDirection = "column";
        docontainer.style.top = "20%";
        headCV.style.height = "100%";
        headCV.style.width = "30%";
        job.style.top = "15%";
        CV.style.display = "flex";
        bodyCV.style.width = "67%";
        bodyCV.style.left = "35%";
        bodyCV.style.paddingTop = "5%";
      }
      if (i == 3) {
        headCV.style.background = "rgb(78, 134, 99)";
        headCV.style.left = "0";
        docontainer.style.flexDirection = "column";
        docontainer.style.top = "20%";
        headCV.style.height = "100%";
        headCV.style.width = "30%";
        job.style.top = "15%";
        CV.style.display = "flex";
        bodyCV.style.width = "67%";
        bodyCV.style.left = "35%";
        bodyCV.style.paddingTop = "5%";
      }
    });
  });