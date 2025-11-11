//Hàm
//Hàm không thực thi khi được khai báo, hàm thực thi khi được gọi
//Hàm có chỗ lưu trữ riêng trong bộ nhớ gọi là function scope, do đó, biến được khai báo trong hàm sẽ không thể truy cập ở ngoài hàm
let ten;
//Hàm khai báo-declairation function
function getData() {
  ten = "Minh";
}
function thongBao() {
  alert("Vip 15 " + ten + " đã đăng nhập");
}
// getData();

// thongBao();
//Hàm biểu thức/ Expression function
const expressionFunciton = function () {
  alert("Hàm biểu thức đã được chạy");
};
// expressionFunciton();
// arrow function
const arrowFunction = () => {
  alert("Arrow function đã hoạt động");
};
// arrowFunction();
//IIFE - Imediately invoke function/ hàm kích hoạt ngay lập tức
// (function () {
//   alert("Hàm IIFE đã hoạt động");
// })();

//Hàm trả về giá trị

const tinhTong = (a, b) => {
  return a + b;
};
// const tong3va4 = tong(3, 4);
// alert(tong3va4);
function inTong(callback) {
  const c = 3;
  const d = 4;
  console.log(callback(c, d));
}
inTong(tinhTong);

//Hàm có tham số
