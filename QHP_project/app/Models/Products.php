<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Products extends Model
{
    use HasFactory;

    protected $table = 'sanpham';

    public function getAllProducts($filters = [], $keywords = ''){
        // $products = DB::select('SELECT MaSP, TenSP, GiaBan, MoTa, HinhAnh, TenTheLoai, TenDanhMuc FROM '. $this->table . ', theloai, danhmuc
        // WHERE ' . $this->table . '.MaTheLoai = theloai.MaTheLoai AND ' . $this->table . '.MaDanhMuc = danhmuc.MaDanhMuc ORDER BY MaSP');
        
        if (empty($filters)){
            $products = DB::select("SELECT MaSP, TenSP, GiaBan, MoTa, HinhAnh, TenTheLoai, TenDanhMuc FROM ". $this->table . " LEFT OUTER JOIN  theloai ON " . 
            $this->table . ".MaTheLoai = theloai.MaTheLoai LEFT OUTER JOIN danhmuc ON " . $this->table . ".MaDanhMuc = danhmuc.MaDanhMuc 
            WHERE TenSP LIKE '%". $keywords ."%' ORDER BY MaSP");
        } else {
            $products = DB::select("SELECT MaSP, TenSP, GiaBan, MoTa, HinhAnh, TenTheLoai, TenDanhMuc FROM ". $this->table . " LEFT OUTER JOIN  theloai ON " . 
            $this->table . '.MaTheLoai = theloai.MaTheLoai LEFT OUTER JOIN danhmuc ON ' . $this->table . 
            ".MaDanhMuc = danhmuc.MaDanhMuc WHERE (danhmuc.MaDanhMuc=".$filters[0]. ") AND (theloai.MaTheLoai=".$filters[1]. ") 
            AND TenSP LIKE '%". $keywords ."%' ORDER BY MaSP");
        }

        return $products;
    }

    public function addProduct($data){
        DB::insert('INSERT INTO '.$this->table.' (TenSP, GiaBan, MoTa, HinhAnh, MaTheLoai, MaDanhMuc)
        VALUES (?, ?, ?, ?, ?, ?)', $data);
    }

    public function getDetail($id){
        return DB::select('SELECT * FROM '.$this->table.' WHERE MaSP = ?', [$id]);
    }

    public function updateProduct($data, $id){
        $data[] = $id;

        return DB::update('UPDATE '.$this->table.' SET 
        TenSP=?,
        GiaBan=?,
        MoTa=?,
        HinhAnh=?,
        MaTheLoai=?,
        MaDanhMuc=?
        WHERE MaSP = ?', $data);
    }

    public function deleteProduct($id){
        $deleteOK = 1;
        //L???y ra c??c m?? chi ti???t s???n ph???m t????ng ???ng v???i m?? s???n ph???m
        $detailID = DB::select('SELECT ChiTietSPID FROM chitietsanpham WHERE MaSP=?', [$id]);
        //Ki???m tra xem c?? ????n h??ng n??o c?? ch???a s???n ph???m c???n x??a hay kh??ng
        foreach($detailID as $item){
            //L???y ra m?? ????n h??ng
            $orderDetail = DB::select('SELECT * FROM chitietdonhang WHERE ChiTietSPID=?', [$item->ChiTietSPID]);
            if (empty($orderDetail)){
                continue;
            } else {
                $deleteOK = 0;
                break;
            }
        }
        
        //N???u kh??ng c?? ????n h??ng n??o c?? s???n ph???m c???n x??a th?? ???????c ph??p x??a s???n ph???m
        if ($deleteOK == 1){
            return DB::delete('DELETE FROM '.$this->table.' WHERE MaSP=?', [$id]);
        } else {
            return False;
        }

    }

    public function searchProduct($key){
        // if (is_numeric($key)){
        //     // return DB::select('SELECT * FROM '.$this->table.' WHERE GiaBan = '.$key);
        //     $variance = 0.01;
        //     return DB::select("SELECT * FROM ".$this->table." WHERE ROUND(ABS(GiaBan - $key), 2) < ".$variance);
        // } else {
        //     return DB::select('SELECT * FROM '.$this->table.' WHERE (TenSP LIKE "%'.$key.'%")');
        // }

        return DB::select("SELECT * FROM ".$this->table." WHERE (TenSP LIKE '%".$key."%') OR GiaBan = '".$key."'");

        //return DB::select("SELECT * FROM ".$this->table." WHERE TenSP LIKE '%$key%' OR GiaBan = '".$key."'");
        // $variance = 0.01;
        // return DB::select("SELECT * FROM ".$this->table." WHERE TenSP LIKE '%$key%' OR ABS(GiaBan - $key) < ".$variance);
    }
}
