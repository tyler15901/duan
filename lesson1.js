// //Khai báo biến : var let const
// {
//   var a = 5;
// }
// {
//   let b = 7;
//   console.log(b);
// }

// //var: khai báo lại, biến toàn cục

// console.log(a);
// console.log(b);
// console.log(c);
// var a = 5;
// var b = "7a";
// console.log(b - a);

// ++ --
// var a = 5;
// var b = a++;
// var c = ++a;
// console.log(a, b, c);
// //
// console.log(0.1 + 0.2);
// Mảng

//Tạo mảng
//Ngầm định
const arrProduct = [1, 2, 3, 4];
// const arrProduct2 = [7, 8, 9];
//Tường minh
// const newArr = new Array(9, 8, 7, 6, 5);
// arrProduct.pop();
// console.log(arrProduct);
//Truy xuất phần tử mảng
// const newArr = arrProduct;
// arrProduct.pop();
// console.log(newArr);

//Spread
// const newArr = [...arrProduct];
// newArr.pop();
// console.log(arrProduct, newArr);
//Phương thức làm việc với mảng
//Thêm đầu mảng: unshift
// arrProduct.unshift(0);
// //Thêm cuối : push()
// arrProduct.push(7);
// arrProduct.shift();
// arrProduct.pop();
// console.log(arrProduct.length);
arrProduct.splice(arrProduct.length - 1, 1);
arrProduct.splice(0, 1);
arrProduct.splice(
  0,
  0,
  "Đây là phần tử được them vào đầu bởi splice",
  5,
  6,
  7,
  8,
  9
);
arrProduct.splice(arrProduct.length - 1);
arrProduct.splice(arrProduct.length, 0, "Đây là phần tử thêm cuối bưởi spic");
console.log(arrProduct);
