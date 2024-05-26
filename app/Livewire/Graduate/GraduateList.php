<?php

namespace App\Livewire\Graduate;

use App\Models\Department;
use App\Models\Graduate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class GraduateList extends Component
{


    use WithPagination;

    public $search = '';
    public $selectedUserIds = [];
    public $userIdsOnPage = [];

    protected $listeners = ['success' => 'refreshTable', 'SelectedDelete' => 'deleteSelected', 'Apply' => 'applyed', 'Reset' => 'reseted'];
    public $joinDateTo;
    public $joinDateFrom;

    public function updatedSearchable($property)
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    public function refreshTable()
    {
        $this->render();
    }

    public function render()
    {
        $departments = Department::all();
        $graduates = Graduate::all();
        $roles = Role::all();
        $query = Graduate::query();

        // تطبيق فلترة الحالة
//        if ($this->status !== '') {
//            $query->where('status', $this->status);
//        }

        // تطبيق فلترة الدور
//        if ($this->selectedRole !== null) {
//            $query->whereHas('roles', function ($query) {
//                $query->where('name', $this->selectedRole);
//            });
//        }
        // تطبيق فلترة تاريخ الانضمام
//        if ($this->joinDateFrom) {
//            $query->where('created_at', '>=', $this->joinDateFrom);
//        }
//        if ($this->joinDateTo) {
//            $query->where('created_at', '<=', $this->joinDateTo);
//        }

        // تطبيق فلترة البحث
//        $query->where(function ($q) {
//            $q->where('name', 'like', '%' . $this->search . '%')
//                ->orWhere('email', 'like', '%' . $this->search . '%');
//        });

        // جلب النتائج المفلترة مع التصفية
        $graduates = $query->paginate(10);

        $this->userIdsOnPage = $graduates->map(fn($graduate) => (string)$graduate->id)->toArray();

        return view('livewire.graduate.graduate-list', ['graduates'=>$graduates, 'roles' => $roles]);
    }

    public function update_status()
    {
        foreach ($this->selectedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->status = $user->status == 0 ? 1 : 0;
                $user->save();
            }
        }

        $this->dispatch('success', 'حالة المستخدمين تم تحديثها بنجاح');
        return $this->render();
    }

    public function deleteSelected()
    {
        if (in_array(Auth::id(), $this->selectedUserIds)) {
            $this->dispatch('error', 'User cannot be deleted');
            return;
        }

        User::destroy($this->selectedUserIds);
        $this->dispatch('success', 'Users successfully deleted');
    }

    public function applyed($selectRole, $selectStatus)
    {
        $this->selectedRole = $selectRole == '' ? null : $selectRole;
        $this->status = $selectStatus;
        $this->resetPage();
    }

    public function reseted()
    {
        $this->selectedRole = null;
        $this->status = '';
        $this->search = '';
        $this->joinDateTo = null;
        $this->joinDateFrom =null;

        $this->resetPage();
    }
//    public function render()
//    {
//        return view('livewire.graduate.graduate-list');
//    }
}
