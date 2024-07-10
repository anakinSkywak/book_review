<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{
    // show giao dien book list
    public function index(Request $request) {

        $book = Book::orderBy('created_at', 'DESC');

        if(!empty($request->keyword)){
            $book->where('title', 'like', '%'.$request->keyword.'%');
        }

        $book = $book->paginate(5);

        return view('books.list', [
            'books' => $book
        ]);
    }
    // tao book
    public function create() {
        return view('books.create');
    }

    public function store(Request $request) {

        $rules = [
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required', 

        ];

        if(!empty($request->image)){
            $rules['image'] = 'image';  
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('books.create')->withInput()->withErrors($validator);
        }

        // luu book vao db
        $book = new Book();
        $book->title = $request->title;
        $book->description = $request->description;
        $book->author = $request->author;
        $book->status = $request->status;
        $book->save();

        // upload imgbook

        if(!empty($request->image)){
            
             // Lấy hình ảnh từ request
             $image = $request->image;
            
             // Lấy phần mở rộng của tệp ảnh
             $ext = $image->getClientOriginalExtension();
             
             // Tạo tên mới cho tệp ảnh
             $imageName = time().'.'.$ext;
             
             // Di chuyển tệp ảnh đến thư mục uploads/profile trong public
             $image->move(public_path('uploads/books/'), $imageName);
         
             // Gán tên tệp ảnh mới cho thuộc tính image của user
             $book->image = $imageName;
             
             // Lưu thông tin user
             $book->save();

             $manager = new ImageManager(Driver::class);
             $img = $manager->read(public_path('uploads/books/'. $imageName)); // 800 x 600 
             $img->resize(190, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
             $img->save(public_path('uploads/books/thumb/'. $imageName));
    
        }

        return redirect()->route('books.index')->with('success', 'thêm sách thành công');

    }
    // show form sua book list
    public function edit($id) {
        $book = Book::findOrFail($id);
        return view('books.edit', [
            'book' => $book
        ]);
    }
    // sua book list
    public function update($id, Request $request) {

        $book = Book::findOrFail($id);

        $rules = [
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required', 

        ];

        if(!empty($request->image)){
            $rules['image'] = 'image';  
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('books.edit', $book->id)->withInput()->withErrors($validator);
        }

        // luu book vao db
       
        $book->title = $request->title;
        $book->description = $request->description;
        $book->author = $request->author;
        $book->status = $request->status;
        $book->save();

        // upload imgbook

        if(!empty($request->image)){

            File::delete(public_path('uploads/books/'. $book->image));
            File::delete(public_path('uploads/books/thumb/'. $book->image));
            
             // Lấy hình ảnh từ request
             $image = $request->image;
            
             // Lấy phần mở rộng của tệp ảnh
             $ext = $image->getClientOriginalExtension();
             
             // Tạo tên mới cho tệp ảnh
             $imageName = time().'.'.$ext;
             
             // Di chuyển tệp ảnh đến thư mục uploads/profile trong public
             $image->move(public_path('uploads/books/'), $imageName);
         
             // Gán tên tệp ảnh mới cho thuộc tính image của user
             $book->image = $imageName;
             
             // Lưu thông tin user
             $book->save();

             $manager = new ImageManager(Driver::class);
             $img = $manager->read(public_path('uploads/books/'. $imageName)); // 800 x 600 
             $img->resize(190, 296, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
             $img->save(public_path('uploads/books/thumb/'. $imageName));
    
        }

        return redirect()->route('books.index')->with('success', 'Sửa sách thành công');
    }
    // xoa book list
    public function destroy(Request $request) {
        $book = Book::find($request->id);

        if($book == null){
            session()->flash('error', 'book not found');
            return response()->json([
                'status' => false,
                'message' => 'book not found'
            ]);
        }
        else{
            File::delete(public_path('uploads/books/'. $book->image));
            File::delete(public_path('uploads/books/thumb/'. $book->image));
            $book->delete();


            session()->flash('success', 'đã xóa thành công');
            return response()->json([
                'status' => true,
                'message' => 'đã xóa thành công '
            ]);
        }
    }
}
