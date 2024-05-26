<?php

namespace App\Livewire\Graduate;

use App\Models\Department;
use App\Models\Graduate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class AddGraduateModel extends Component
{
    use WithFileUploads;

    public $user_id;
    public $graduate_id;
    public $name;
    public $email;
    public $department; // تعديل الاسم هنا
    public $for_year;
    public $stdst;
    public $avatar;
    public $saved_avatar;

    public $edit_mode = false;

    protected $rules = [
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'avatar' => 'nullable|sometimes|image|max:1024',
        'department' => 'required', // تعديل الاسم هنا
        'stdst' => 'required',
        'for_year' => 'required',
    ];

    protected $listeners = [
        'delete_user' => 'deleteUser',
        'update_user' => 'updateUser',
        'departmentselect' => 'deparSelect',
    ];
    private $role ='خريج';

    public function render()
    {
        $departments = Department::all();
        return view('livewire.graduate.add-graduate-model', compact('departments'));
    }

    public function submit()
    {
        dd($this->department);
        // Validate the form input data
        $this->validate();

        DB::transaction(function () {
            // Prepare the data for creating a new user
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'stdst' => $this->stdst,
                'profile_photo_path' => $this->avatar ? $this->avatar->store('avatars', 'public') : null,
            ];

            if (!$this->edit_mode) {
                $data['password'] = Hash::make($this->email);
            }

            // Update or Create a new user record in the database
            $user = User::find($this->user_id) ?? User::create($data);
            $gradData = [
                'department_id' => $this->department,
                'for_year' => $this->for_year,
                'user_id' => $user->id,
            ];

            $graduate = Graduate::find($this->graduate_id) ?? Graduate::create($gradData);

            if ($this->edit_mode) {
                $user->update($data);
                $graduate->update($gradData);
                $this->dispatch('success', __('User updated'));
            } else {
                if (!Role::findByName('خريج')) {
                    Role::create(['name' => 'خريج']);
                }
                $user->assignRole('خريج');
                $this->dispatch('success', __('New user created'));
            }
        });

        // Reset the form fields after successful submission
        $this->reset();
    }

    public function deleteUser($id)
    {
        if ($id == Auth::id()) {
            $this->dispatch('error', 'لا يمكن حذف المستخدم الحالي');
            return;
        }

        $user = User::find($id);

        if ($user) {
            if (!empty($user->profile_photo_path) && Storage::exists($user->profile_photo_path)) {
                Storage::delete($user->profile_photo_path);
            }
            $user->delete();
            $this->dispatch('success', 'تم حذف المستخدم بنجاح');
        } else {
            $this->dispatch('error', 'المستخدم غير موجود');
        }
    }

    public function updateUser($id)
    {
        $this->edit_mode = true;

        $graduate = Graduate::find($id);
        $user = User::find($graduate->user_id);

        $this->user_id = $user->id;
        $this->graduate_id = $graduate->id;
        $this->saved_avatar = $user->profile_photo_url;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department = $graduate->department_id;
        $this->for_year = $graduate->for_year;
        $this->stdst = $user->stdst;
    }
    public function deparSelect($depar){

        dd($depar);
        return $this->department =$depar;
    }

    public function hydrate()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
