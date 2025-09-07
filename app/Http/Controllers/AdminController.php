<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function index()
    {
        return view("admin.index");
    }
    
    public function brands()
    {
            $brands = Brand::orderBy('id','DESC')->paginate(10);
            return view("admin.brands",compact('brands'));
    }

    public function add_brand()
    {
        return view("admin.brand-add");
    }
    
    public function add_brand_store(Request $request)
    {
        Log::info('=== Add Brand Store START ===');

        // Log raw input
        Log::info('Request all data', $request->all());

        // Log informasi file mentah (kalau ada)
        if ($request->hasFile('image')) {
            $imgFile = $request->file('image');
            Log::info('Image raw file info', [
                'original_name' => $imgFile->getClientOriginalName(),
                'extension'     => $imgFile->getClientOriginalExtension(),
                'mime_type'     => $imgFile->getMimeType(),
                'size'          => $imgFile->getSize(),
                'tmp_path'      => $imgFile->getPathname(),
            ]);
        } else {
            Log::warning('No image file detected in request');
        }

        // Validasi
        try {
            $request->validate([
                'name'  => 'required',
                'slug'  => 'required|unique:brands,slug',
                'image' => 'nullable|mimes:png,jpg,jpeg,webp|max:2048'
            ]);
            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        Log::info('Brand model initialized', $brand->toArray());

        // Proses upload gambar jika ada
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $file_extention = $image->extension();
                $file_name = Carbon::now()->timestamp . '.' . $file_extention;
                Log::info('Generated file name', ['file_name' => $file_name]);

                $this->GenerateBrandThumbnailsImage($image, $file_name);
                Log::info('Thumbnail generated successfully');

                $brand->image = $file_name;
            } catch (\Exception $e) {
                Log::error('Error processing image', ['error' => $e->getMessage()]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process image',
                    'error'   => $e->getMessage()
                ], 500);
            }
        }

        // Simpan ke DB
        try {
            $brand->save();
            Log::info('Brand saved successfully', ['brand_id' => $brand->id]);
        } catch (\Exception $e) {
            Log::error('Error saving brand', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save brand',
                'error'   => $e->getMessage()
            ], 500);
        }

        Log::info('=== Add Brand Store END ===');

        // Balikin JSON buat debug di Network
        return response()->json([
            'status' => 'success',
            'message' => 'Record has been added successfully!',
            'brand' => $brand
        ]);
    }

    public function edit_brand($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit',compact('brand'));
    }

    public function update_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        if($request->hasFile('image'))
        {            
            if (File::exists(public_path('uploads/brands').'/'.$brand->image)) {
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbnailsImage($image,$file_name);
            $brand->image = $file_name;
        }        
        $brand->save();        
        return redirect()->route('admin.brands')->with('status','Record has been updated successfully !');
    }

    public function delete_brand($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands').'/'.$brand->image)) {
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Record has been deleted successfully !');
    }

    public function categories()
    {
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view("admin.categories",compact('categories'));
    }

    public function add_category()
    {
        return view("admin.category-add");
    }

    public function add_category_store(Request $request)
    {        
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbnailsImage($image,$file_name);
        $category->image = $file_name;        
        $category->save();
        return redirect()->route('admin.categories')->with('status','Record has been added successfully !');
    }

    public function edit_category($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit',compact('category'));
    }

    public function update_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        if($request->hasFile('image'))
        {            
            if (File::exists(public_path('uploads/categories').'/'.$category->image)) {
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbnailsImage($image,$file_name);   
            $category->image = $file_name;
        }        
        $category->save();    
        return redirect()->route('admin.categories')->with('status','Record has been updated successfully !');
    }

    public function delete_category($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories').'/'.$category->image)) {
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status','Record has been deleted successfully !');
    }

    public function products()
    {
        $products = Product::OrderBy('created_at','DESC')->paginate(10);        
        return view("admin.products",compact('products'));
    }

    public function add_product()
    {
        $categories = Category::Select('id','name')->orderBy('name')->get();
        $brands = Brand::Select('id','name')->orderBy('name')->get();
        return view("admin.product-add",compact('categories','brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'slug'              => 'required|unique:products,slug',
            'category_id'       => 'required',
            'brand_id'          => 'required',            
            'short_description' => 'required',
            'description'       => 'required',
            'SKU'               => 'required',
            'stock_status'      => 'required',
            'featured'          => 'required',
            'image'             => 'required|mimes:png,jpg,jpeg|max:2048',
            'variants'          => 'required|json'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->slug);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->variants = $request->variants; // sudah JSON

        // handle image utama
        $current_timestamp = Carbon::now()->timestamp;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();
            $this->GenerateThumbnailsImage($image, $imageName);            
            $product->image = $imageName;
        }

        // handle gallery images
        $gallery_arr = [];
        if ($request->hasFile('images')) {
            $allowedfileExtension = ['jpg','png','jpeg'];
            $counter = 1;
            foreach ($request->file('images') as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array($gextension, $allowedfileExtension)) {
                    $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateThumbnailsImage($file, $gfilename);
                    $gallery_arr[] = $gfilename;
                    $counter++;
                }
            }
        }
        $product->images = implode(',', $gallery_arr);

        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $product->save();

        return redirect()->route('admin.products')
            ->with('status', 'Record has been added successfully!');
    }

    public function edit_product($id)
    {
        $product = Product::find($id);
        $categories = Category::Select('id','name')->orderBy('name')->get();
        $brands = Brand::Select('id','name')->orderBy('name')->get();
        return view('admin.product-edit',compact('product','categories','brands'));
    }

    public function update_product(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:products,slug,'.$request->id,
            'category_id'=>'required',
            'brand_id'=>'required',            
            'short_description'=>'required',
            'description'=>'required',
            'SKU'=>'required',
            'stock_status'=>'required',
            'featured'=>'required',
            'quantity'=>'required',
            'image'=>'mimes:png,jpg,jpeg|max:2048'            
        ]);
        
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $current_timestamp = Carbon::now()->timestamp;
        
        if($request->hasFile('image'))
        {        
            $product->image = $request->image;
            $file_extention = $request->file('image')->extension();            
            $file_name = $current_timestamp . '.' . $file_extention;
            $path = $request->image->storeAs('products', $file_name, 'public_uploads');
            $product->image = $path;
        }
        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images'))
        {
            $allowedfileExtension=['jpg','png','jpeg'];
            $files = $request->file('images');
            foreach($files as $file){                
                $gextension = $file->getClientOriginalExtension();                                
                $check=in_array($gextension,$allowedfileExtension);            
                if($check)
                {
                    $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;                    
                    $this->GenerateThumbnailsImage($file, $gfilename);
                    array_push($gallery_arr,$gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(', ', $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();       
        return redirect()->route('admin.products')->with('status','Record has been updated successfully !');
    }

    public function delete_product($id)
    {
        $product = Product::find($id);        
        $product->delete();
        return redirect()->route('admin.products')->with('status','Record has been deleted successfully !');
    } 

    public function coupons()
    {
            $coupons = Coupon::orderBy("expiry_date","DESC")->paginate(12);
            return view("admin.coupons",compact("coupons"));
    }

    public function add_coupon()
    {        
        return view("admin.coupon-add");
    }

    public function add_coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route("admin.coupons")->with('status','Record has been added successfully !');
    }

    public function edit_coupon($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon-edit',compact('coupon'));
    }

    public function update_coupon(Request $request)
    {
        $request->validate([
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date'
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;               
        $coupon->save();           
        return redirect()->route('admin.coupons')->with('status','Record has been updated successfully !');
    }

    public function delete_coupon($id)
    {
            $coupon = Coupon::find($id);        
            $coupon->delete();
            return redirect()->route('admin.coupons')->with('status','Record has been deleted successfully !');
    }

    public function orders()
    {
            $orders = Order::orderBy('created_at','DESC')->paginate(12);
            return view("admin.orders",compact('orders'));
    }

    public function order_items($order_id)
    {
        $order = Order::find($order_id);
        $orderitems = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id',$order_id)->first();
        return view("admin.order-details",compact('order','orderitems','transaction'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);

        $newStatus = $request->order_status;

        // Validasi: delivered hanya bisa kalau transaksi sudah settlement atau approved
        if ($newStatus === 'delivered' && $order->transaction && !in_array($order->transaction->status, ['settlement','approved'])) {
            return back()->with('status', 'Cannot mark as delivered before transaction is settled or approved.');
        }

        $order->status = $newStatus;

        switch ($newStatus) {
            case 'delivered':
                $order->delivered_date = Carbon::now();
                break;
            case 'canceled':
                $order->canceled_date = Carbon::now();
                break;
            default:
                $order->delivered_date = null;
                $order->canceled_date = null;
                break;
        }

        $order->save();

        return back()->with('status', 'Order status changed successfully!');
    }

    public function GenerateThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        // Resize tanpa crop, biar sesuai aspect ratio
        $img->resize(800, 800, function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // biar gambar kecil gak jadi pecah
        })->save($destinationPath.'/'.$imageName);
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }
}
