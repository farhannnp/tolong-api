@extends('layouts.app')

@section('title', 'Schedule - Skill Exchange')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Schedule</h1>
        <button onclick="openCreateScheduleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            New Schedule
        </button>
    </div>

    <!-- Schedules Table -->
    @if($schedules->count() > 0)
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @foreach($schedules as $schedule)
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-blue-600 truncate">
                                    Session with {{ $schedule->user1_id == auth()->id() ? $schedule->user2->name : $schedule->user1->name }}
                                </p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                       {{ $schedule->status == 'upcoming' ? 'bg-green-100 text-green-800' : 
                                          ($schedule->status == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($schedule->status) }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $schedule->scheduled_at->format('M d, Y') }} {{$schedule->id}}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $schedule->scheduled_at->format('H:i') }}
                                    </p>
                                    <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                        <i class="fas fa-{{ $schedule->method == 'online' ? 'video' : 'map-marker-alt' }} mr-1"></i>
                                        {{ ucfirst($schedule->method) }}
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                    <div class="flex space-x-2">
                                        <button onclick="editSchedule({{ $schedule->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteSchedule({{ $schedule->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @if($schedule->notes)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">{{ $schedule->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $schedules->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-calendar text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No schedules yet</h3>
        <p class="text-gray-600 mb-6">Create your first schedule to start collaborating!</p>
        <button onclick="openCreateScheduleModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-plus mr-2"></i>
            Create Schedule
        </button>
    </div>
    @endif
</div>

<!-- Create Schedule Modal -->
<div id="createScheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create New Schedule</h3>
                <button onclick="closeCreateScheduleModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('schedule.store') }}" method="POST">
            <!-- <form action="/schedules" method="POST"> -->
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="user2_id" class="block text-sm font-medium text-gray-700">Select User</label>
                        <select name="user2_id" id="user2_id" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->department }} - {{ $user->batch }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="scheduled_date" id="scheduled_date" required 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="scheduled_time" id="scheduled_time" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="method" class="block text-sm font-medium text-gray-700">Method</label>
                        <select name="method" id="method" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add any additional notes or agenda..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeCreateScheduleModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="editScheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Schedule</h3>
                <button onclick="closeEditScheduleModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT') {{-- Metode PUT untuk update --}}

                <input type="hidden" name="schedule_id" id="editScheduleId"> {{-- Untuk menyimpan ID jadwal --}}

                <div class="space-y-4">
                    <div>
                        <label for="editScheduleTitle" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="editScheduleTitle" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="editScheduleDescription" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="description" id="editScheduleDescription" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="editScheduleDate" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="editScheduleDate" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editScheduleTime" class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" id="editScheduleTime" required 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="editScheduleStatus" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="editScheduleStatus" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="upcoming">Upcoming</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditScheduleModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Schedule Modal -->
<div id="deleteScheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Schedule</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this schedule? This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button onclick="closeDeleteScheduleModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <form id="deleteScheduleForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Schedule modal functions
    function openCreateScheduleModal() {
        document.getElementById('createScheduleModal').classList.remove('hidden');
    }
    
    function closeCreateScheduleModal() {
        document.getElementById('createScheduleModal').classList.add('hidden');
    }
    
    function closeEditScheduleModal() {
        document.getElementById('editScheduleModal').classList.add('hidden');
    }
    
    function closeDeleteScheduleModal() {
        document.getElementById('deleteScheduleModal').classList.add('hidden');
    }

    // Edit schedule
    function editSchedule(scheduleId) {
        fetch(`/api/schedules/${scheduleId}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editScheduleId').value = data.id;
            document.getElementById('editScheduleTitle').value = data.title;
            document.getElementById('editScheduleDescription').value = data.description || '';

            const startDate = new Date(data.start_time);
            document.getElementById('editScheduleDate').value = startDate.toISOString().split('T')[0];
            document.getElementById('editScheduleTime').value = startDate.toTimeString().split(' ')[0].substring(0, 5);

            const statusSelect = document.getElementById('editScheduleStatus');
            Array.from(statusSelect.options).forEach(option => {
                option.selected = (option.value === data.status);
            });

            document.getElementById('editScheduleForm').action = `/schedule/${data.id}`;
            document.getElementById('editScheduleModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching schedule data:', error);
            alert('Gagal mengambil data jadwal. Silakan coba lagi. Cek console browser untuk detail.');
        });
    }
   
    // Delete schedule
    function deleteSchedule(scheduleId) {
        document.getElementById('deleteScheduleModal').classList.remove('hidden');
        document.getElementById('deleteScheduleForm').action = `/schedule/${scheduleId}`;
    }
    
    // Combine date and time for scheduled_at
    document.querySelector('#createScheduleModal form').addEventListener('submit', function(e) {
        const date = document.getElementById('scheduled_date').value;
        const time = document.getElementById('scheduled_time').value;
        
        if (date && time) {
            const scheduledAt = date + ' ' + time;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'scheduled_at';
            hiddenInput.value = scheduledAt;
            this.appendChild(hiddenInput);
        }
    });
    
    document.querySelector('#editScheduleModal form').addEventListener('submit', function(e) {
        const date = document.getElementById('editScheduledDate').value;
        const time = document.getElementById('editScheduledTime').value;
        
        if (date && time) {
            const scheduledAt = date + ' ' + time;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'scheduled_at';
            hiddenInput.value = scheduledAt;
            this.appendChild(hiddenInput);
        }
    });
</script>
@endsection