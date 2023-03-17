var tabs = document.querySelectorAll('#v-pills-tabContent textarea');


for(let element of tabs)
{
    
    
}

tinymce.init({
    selector: '#v-pills-tabContent textarea',
    skin: 'tinymce-5',
    plugins: 'lists, link, image, media, code',
    toolbar: 'h1 h2 bold italic strikethrough blockquote bullist numlist backcolor   | link image media | code removeformat help',
    menubar: 'insert, format, ',
    branding: false,
    height: 300,
    width: 700,
    promotion:false,
    setup: function(ed) {
        ed.on('change', function(e) { ed.save(); });
    }
    
  });


// tinymce.init({
//     selector: '#v-pills-tabContent textarea',
//     skin: 'bootstrap',
//     plugins: 'lists, link, image, media, textcolor',
//     toolbar: 'h1 h2 bold italic strikethrough blockquote bullist numlist backcolor forecolor  | link image media |  removeformat help',
//     menubar: 'insert, format,  ',
//     branding: false,
//     height: 300,
//     setup: function(ed) {
//         ed.on('change', function(e) { ed.save(); });
//     }
    
//   });

//   tinymce.init({ 
//     selector: 'textarea',
//     setup: function (editor) {
//         editor.on('change', function (e) {
//             editor.save();
//         });
//     }
// });