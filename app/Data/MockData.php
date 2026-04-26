<?php

namespace App\Data;

class MockData
{
    public static function menuItems(): array
    {
        return [
            ['id'=>'1','name'=>'Mì trộn đặc biệt','price'=>45000,'category'=>'noodles','image'=>'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=400&q=80','rating'=>4.8,'sold'=>523,'distance'=>'0.8km','description'=>'Mì trộn với sốt đặc biệt của nhà, topping trứng lòng đào, thịt bò slice','calories'=>420,'isNew'=>false,'isBestSeller'=>true,'toppings'=>[['id'=>'t1','name'=>'Trứng lòng đào','price'=>8000],['id'=>'t2','name'=>'Thịt bò thêm','price'=>15000],['id'=>'t3','name'=>'Cải thêm','price'=>3000]],'sizes'=>[['id'=>'s1','name'=>'S','price'=>0],['id'=>'s2','name'=>'M','price'=>5000],['id'=>'s3','name'=>'L','price'=>10000]]],
            ['id'=>'2','name'=>'Trà sữa trân châu đen','price'=>35000,'category'=>'drinks','image'=>'https://images.unsplash.com/photo-1572932759882-bb34c848d1b3?w=400&q=80','rating'=>4.9,'sold'=>1200,'distance'=>'0.8km','description'=>'Trà sữa pha chế theo công thức độc quyền, trân châu đen dai giòn','calories'=>280,'isNew'=>false,'isBestSeller'=>true,'toppings'=>[['id'=>'t5','name'=>'Thạch cà phê','price'=>5000],['id'=>'t6','name'=>'Pudding trứng','price'=>7000]],'sizes'=>[['id'=>'s1','name'=>'M','price'=>0],['id'=>'s2','name'=>'L','price'=>5000],['id'=>'s3','name'=>'XL','price'=>10000]]],
            ['id'=>'3','name'=>'Bánh mì đặc biệt','price'=>30000,'category'=>'snacks','image'=>'https://images.unsplash.com/photo-1599719455360-ff0be7c4dd06?w=400&q=80','rating'=>4.7,'sold'=>890,'distance'=>'0.8km','description'=>'Bánh mì giòn với nhân pate, chả lụa, dưa leo tươi và rau mùi','calories'=>380,'isNew'=>true,'isBestSeller'=>false,'toppings'=>[['id'=>'t8','name'=>'Trứng ốp la','price'=>6000],['id'=>'t9','name'=>'Chả lụa thêm','price'=>8000]],'sizes'=>[['id'=>'s1','name'=>'Nhỏ','price'=>0],['id'=>'s2','name'=>'Lớn','price'=>8000]]],
            ['id'=>'4','name'=>'Gà rán giòn cay','price'=>55000,'category'=>'snacks','image'=>'https://images.unsplash.com/photo-1765360024320-b2ab819c6f75?w=400&q=80','rating'=>4.6,'sold'=>445,'distance'=>'0.8km','description'=>'Gà rán tẩm ướp cay đặc biệt, giòn ngoài mềm trong','calories'=>560,'isNew'=>true,'isBestSeller'=>false,'toppings'=>[['id'=>'t11','name'=>'Sốt mayo','price'=>3000],['id'=>'t12','name'=>'Sốt cay đặc biệt','price'=>4000]],'sizes'=>[['id'=>'s1','name'=>'2 miếng','price'=>0],['id'=>'s2','name'=>'4 miếng','price'=>25000]]],
            ['id'=>'5','name'=>'Sinh tố xoài nhiệt đới','price'=>40000,'category'=>'drinks','image'=>'https://images.unsplash.com/photo-1595981267035-7b04ca84a82d?w=400&q=80','rating'=>4.7,'sold'=>320,'distance'=>'0.8km','description'=>'Sinh tố xoài Cát Hòa Lộc tươi, không pha thêm nước','calories'=>210,'isNew'=>false,'isBestSeller'=>false,'toppings'=>[['id'=>'t13','name'=>'Thêm xoài','price'=>10000]],'sizes'=>[['id'=>'s1','name'=>'M','price'=>0],['id'=>'s2','name'=>'L','price'=>8000]]],
            ['id'=>'6','name'=>'Phở bò đặc biệt','price'=>65000,'category'=>'noodles','image'=>'https://images.unsplash.com/photo-1677011454858-8ecb6d4e6ce0?w=400&q=80','rating'=>4.9,'sold'=>678,'distance'=>'0.8km','description'=>'Phở bò nấu 12 tiếng với xương ống, thịt bò tái chín đặc biệt','calories'=>480,'isNew'=>false,'isBestSeller'=>true,'toppings'=>[['id'=>'t15','name'=>'Tái thêm','price'=>15000],['id'=>'t16','name'=>'Gân giòn','price'=>12000]],'sizes'=>[['id'=>'s1','name'=>'Nhỏ','price'=>0],['id'=>'s2','name'=>'Vừa','price'=>8000],['id'=>'s3','name'=>'Lớn','price'=>15000]]],
            ['id'=>'7','name'=>'Cơm tấm sườn bì chả','price'=>55000,'category'=>'rice','image'=>'https://images.unsplash.com/photo-1588703314135-01e9e4205802?w=400&q=80','rating'=>4.8,'sold'=>412,'distance'=>'0.8km','description'=>'Cơm tấm sườn nướng mật ong, bì sợi và chả trứng truyền thống','calories'=>620,'isNew'=>false,'isBestSeller'=>true,'toppings'=>[['id'=>'t18','name'=>'Sườn thêm','price'=>20000],['id'=>'t19','name'=>'Trứng ốp','price'=>6000]],'sizes'=>[['id'=>'s1','name'=>'Thường','price'=>0],['id'=>'s2','name'=>'Đầy đặn','price'=>15000]]],
            ['id'=>'8','name'=>'Chả giò giòn','price'=>25000,'category'=>'snacks','image'=>'https://images.unsplash.com/photo-1776178393305-be4c1097fae5?w=400&q=80','rating'=>4.5,'sold'=>289,'distance'=>'0.8km','description'=>'Chả giò chiên giòn nhân tôm thịt, rau củ tươi ngon','calories'=>320,'isNew'=>false,'isBestSeller'=>false,'toppings'=>[['id'=>'t21','name'=>'Nước mắm chua ngọt','price'=>2000]],'sizes'=>[['id'=>'s1','name'=>'3 cái','price'=>0],['id'=>'s2','name'=>'6 cái','price'=>20000]]],
        ];
    }

    public static function product(string $id): array
    {
        $items = collect(self::menuItems())->keyBy('id');
        return $items[$id] ?? ['id'=>$id,'name'=>'Sản phẩm','price'=>45000,'image'=>'','description'=>'','rating'=>4.8,'sold'=>500,'distance'=>'0.8km','calories'=>420,'isNew'=>false,'isBestSeller'=>false,'sizes'=>[],'toppings'=>[]];
    }

    public static function combos(): array
    {
        return [
            ['id'=>'c1','name'=>'Combo Văn phòng A','description'=>'Mì trộn + Trà sữa M','originalPrice'=>80000,'comboPrice'=>65000,'savings'=>15000,'image'=>'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=300&q=80'],
            ['id'=>'c2','name'=>'Combo Bựa B','description'=>'Gà rán 4 miếng + Trà sữa L','originalPrice'=>115000,'comboPrice'=>90000,'savings'=>25000,'image'=>'https://images.unsplash.com/photo-1765360024320-b2ab819c6f75?w=300&q=80'],
            ['id'=>'c3','name'=>'Combo Phở Deluxe','description'=>'Phở bò + Sinh tố xoài','originalPrice'=>105000,'comboPrice'=>85000,'savings'=>20000,'image'=>'https://images.unsplash.com/photo-1677011454858-8ecb6d4e6ce0?w=300&q=80'],
        ];
    }

    public static function reviews(): array
    {
        return [
            ['id'=>'r1','user'=>'Minh Tuấn','avatar'=>'MT','rating'=>5,'comment'=>'Mì trộn ngon cực! Sốt đặc biệt lắm, ăn là nghiện luôn 🔥','item'=>'Mì trộn đặc biệt','time'=>'2 giờ trước','img'=>'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=100&q=80'],
            ['id'=>'r2','user'=>'Lan Anh','avatar'=>'LA','rating'=>5,'comment'=>'Trà sữa nhà này trân châu dai giòn, không ngọt quá. Thích!','item'=>'Trà sữa trân châu','time'=>'5 giờ trước','img'=>'https://images.unsplash.com/photo-1572932759882-bb34c848d1b3?w=100&q=80'],
            ['id'=>'r3','user'=>'Hoàng Nam','avatar'=>'HN','rating'=>5,'comment'=>'Giao hàng nhanh, đồ ăn còn nóng hổi. Ship có túi giữ nhiệt xịn 👍','item'=>'Phở bò đặc biệt','time'=>'1 ngày trước','img'=>'https://images.unsplash.com/photo-1677011454858-8ecb6d4e6ce0?w=100&q=80'],
            ['id'=>'r4','user'=>'Thu Hà','avatar'=>'TH','rating'=>5,'comment'=>'Combo văn phòng siêu hợp lý, order mỗi ngày cho cả team 😍','item'=>'Combo Văn phòng A','time'=>'3 ngày trước','img'=>'https://images.unsplash.com/photo-1588703314135-01e9e4205802?w=100&q=80'],
        ];
    }

    public static function cartItems(): array
    {
        return [
            ['id'=>'1','menuItemId'=>'1','name'=>'Mì trộn đặc biệt','image'=>'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=400&q=80','price'=>45000,'quantity'=>2,'size'=>'M','toppings'=>['Trứng lòng đào'],'note'=>'Ít hành'],
            ['id'=>'2','menuItemId'=>'2','name'=>'Trà sữa trân châu đen','image'=>'https://images.unsplash.com/photo-1572932759882-bb34c848d1b3?w=400&q=80','price'=>35000,'quantity'=>1,'size'=>'L','toppings'=>[],'note'=>''],
        ];
    }

    public static function orderHistory(): array
    {
        return [
            ['id'=>'ORD-198','date'=>'20/04/2026','items'=>['Mì trộn đặc biệt x2','Trà sữa x1'],'total'=>125000,'status'=>'completed'],
            ['id'=>'ORD-185','date'=>'18/04/2026','items'=>['Phở bò đặc biệt x1','Sinh tố xoài x1'],'total'=>105000,'status'=>'completed'],
            ['id'=>'ORD-171','date'=>'15/04/2026','items'=>['Combo Văn phòng A x3'],'total'=>195000,'status'=>'completed'],
        ];
    }
}
