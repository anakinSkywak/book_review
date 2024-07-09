<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    // phương thức show register
    public function register() {
        return view('account.register');
    }

    // phương thức đăng ký user
    public function processRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:8',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);

        // tra ve thong bao khi dang ky khong thanh cong
        if($validator->fails()){
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        // dang ky user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.login')->with('success', 'Bạn đẵ đăng ký thành công');
    }

    // function show login
    public function login(){
        return view('account.login');
    }
    // function thuc hien dang nhap 
    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }

        if(Auth::attempt(['email' => $request->email , 'password' => $request->password])) {
            return redirect()->route('account.profile');
        }
        else{
            return redirect()->route('account.login')->with('error', 'Đăng nhập thất bại');
        }
    }

    // function show profile
    public function profile() {
        // tim user theo id
        $user = User::find(Auth::user()->id);

        return view('account.profile', [
            'user' => $user
        ]);
    }

    // update profile
    public function updateProfile(Request $request) {

        $rules = [
            'name' => 'required|min:8',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id.',id',
        ];

        if (!empty($request->image)) {
                $rules['image'] = 'image';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
        }

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if (!empty($request->image)) {
            // Lấy hình ảnh từ request
            $image = $request->image;
            
            // Lấy phần mở rộng của tệp ảnh
            $ext = $image->getClientOriginalExtension();
            
            // Tạo tên mới cho tệp ảnh
            $imageName = time().'.'.$ext;
            
            // Di chuyển tệp ảnh đến thư mục uploads/profile trong public
            $image->move(public_path('uploads/profile'), $imageName);
        
            // Gán tên tệp ảnh mới cho thuộc tính image của user
            $user->image = $imageName;
            
            // Lưu thông tin user
            $user->save();
        }
        
        

        return redirect()->route('account.profile')->with('success', 'bạn đã update profile thành công');
    }

    // đang xuat
    public function logout() {
        Auth::logout();
        return redirect()->route('account.login');
    }
}
