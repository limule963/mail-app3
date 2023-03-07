let play = document.getElementsByClassName('play');
// let play2 = document.getElementById('play');

let src1  = "/assets/images/svg/play.svg";
let src2  = "/assets/images/svg/pause.svg";

// let xhr =new XMLHttpRequest;
// let url ='https://localhost:8000/app/compaign/lunch/7';
// xhr.open('GET',url);
// xhr.responseType('json');
// xhr.send();

// xhr.onload()=function(){
//     if(xhr.status = 200) 
//     {
//         alert(JSON.stringify);
//     }
// };

for (let index = 0; index < play.length; index++) {
    const element = play[index];
    element.setAttribute('src',src2);
    
}