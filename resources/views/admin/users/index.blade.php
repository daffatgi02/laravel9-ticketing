@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-user-plus me-1"></i> Add User
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ url('/admin/users') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Regular User</option>
                        <option value="it" {{ request('role') == 'it' ? 'selected' : '' }}>IT Support</option>
                        <option value="ga" {{ request('role') == 'ga' ? 'selected' : '' }}>GA Support</option>
                        <option value="hc" {{ request('role') == 'hc' ? 'selected' : '' }}>HC Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'hc')
                                    <span class="badge bg-dark">HC Admin</span>
                                @elseif($user->role == 'it')
                                    <span class="badge bg-primary">IT Support</span>
                                @elseif($user->role == 'ga')
                                    <span class="badge bg-success">GA Support</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </td>
                            <td>{{ optional($user->department)->name }}</td>
                            <td>{{ $user->position }}</td>
                            <td>
                                @if($user->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary edit-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->role }}"
                                        data-department="{{ $user->department_id }}"
                                        data-employee-id="{{ $user->employee_id }}"
                                        data-position="{{ $user->position }}"
                                        data-phone="{{ $user->phone }}"
                                        data-active="{{ $user->active }}"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(Auth::id() != $user->id)
                                <button type="button" class="btn btn-sm btn-outline-danger delete-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">No users found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($users) && $users->hasPages())
        <div class="card-footer">
            {{ $users->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Modal body content remains unchanged -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="user">Regular User</option>
                                <option value="it">IT Support</option>
                                <option value="ga">GA Support</option>
                                <option value="hc">HC Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_department_id" class="form-label">Department</label>
                            <select class="form-select" id="edit_department_id" name="department_id">
                                <option value="">Select Department</option>
                                @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="edit_employee_id" name="employee_id">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_position" class="form-label">Position/Title</label>
                            <input type="text" class="form-control" id="edit_position" name="position">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user: <strong id="delete_user_name"></strong>?</p>
                <p class="text-danger">This action cannot be undone and may affect ticket assignments.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit User
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const role = this.getAttribute('data-role');
                const department = this.getAttribute('data-department');
                const employeeId = this.getAttribute('data-employee-id');
                const position = this.getAttribute('data-position');
                const phone = this.getAttribute('data-phone');
                const active = this.getAttribute('data-active');

                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_department_id').value = department || '';
                document.getElementById('edit_employee_id').value = employeeId || '';
                document.getElementById('edit_position').value = position || '';
                document.getElementById('edit_phone').value = phone || '';
                document.getElementById('edit_active').checked = active === '1';

                document.getElementById('editUserForm').action = "{{ route('admin.users.update', '') }}/" + id;
            });
        });

        // Delete User
        document.querySelectorAll('.delete-user').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('delete_user_name').textContent = name;
                document.getElementById('deleteUserForm').action = "{{ route('admin.users.destroy', '') }}/" + id;
            });
        });
    });
</script>
@endsection
