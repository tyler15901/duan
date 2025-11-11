// const a = 8;

// console.log(document);
// //Truy xuất 1 phần tử
// const div1 = document.getElementById("target");
// const div2 = document.querySelector("#target2");
// div1.innerHTML = "Text được thay dổi bằng JavaScript";
// console.log(div1, div2);
// //Truy xuất nhiều phần tử
// const divs = document.querySelectorAll("div");
// const testDivs = document.querySelectorAll("#target");
// console.log(divs, testDivs);
// divs[0].innerHTML = "changed text";
// console.log(document.getElementById("parent").firstChild);
const divss = document.getElementById("target")

function xoa(){
    const button = event.target
    console.log(button)
    button.parentElement.parentElement.remove()
}