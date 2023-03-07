let play = document.getElementsByClassName('play');
// let play2 = document.getElementById('play');

let src1  = "/assets/images/svg/play.svg";
let src2  = "/assets/images/svg/pause.svg";

    
    function receive(url,xhr,element,src1,src2,id){
        // let xhr =new XMLHttpRequest;
        // let url ='https://localhost:8000/app/compaign/lunch/7';/
        xhr.open('GET',url);
        xhr.responseType('json');
        xhr.send();

        xhr.onload()=function(){
            if(xhr.status = 200) 
            {
                res = JSON.parse(xhr.response);
                if(url == 'https://localhost:8000/app/compaign/lunch/'+id && res.response == true) element.setAttribute('src',src2)
                else if(url == 'https://localhost:8000/app/compaign/pause/'+id && res.response == true) element.setAttribute('src',src1)
            }
        };

    };

for (let index = 0; index < play.length; index++) {
    const element = play[index];
    let att = element.getAttribute('src');
    let id = element.getAttribute('id');
    let url = '';
    if(att == src1) url = 'https://localhost:8000/app/compaign/lunch/'+id;
    else if(att == src2) url = 'https://localhost:8000/app/compaign/pause/'+id;
    xhr = [];
    xhr.push()  = new XMLHttpRequest;
    receive(url,xhr[index],src1,src2,id);
    // element.setAttribute('src',src2);
    
}