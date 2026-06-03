@extends('layouts.admin')
@section('content')
<div class="p-6">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-gray-800">Activity Logs</h1>
 <p class="text-gray-600 mt-2">System activity history</p>
 </div>
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <table class="min-w-full">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">User</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Action</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Entity</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Details</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">IP Address</th>
 <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Date</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-gray-200">
 @forelse($activityLogs as $log)
 <tr class="hover:bg-gray-50 transition">
 <td class="px-6 py-4 font-semibold text-gray-800">{{ $log->user->name ?? 'System' }}</td>
 <td class="px-6 py-4">
 <span class="px-3 py-1 rounded-full text-xs font-semibold 
 @if(str_contains(strtolower($log->action), 'create')) bg-primary-100 text-primary-800
 @elseif(str_contains(strtolower($log->action), 'update')) bg-primary-100 text-primary-800
 @elseif(str_contains(strtolower($log->action), 'delete')) bg-red-100 text-red-800
 @elseif(str_contains(strtolower($log->action), 'login')) bg-primary-100 text-primary-800
 @elseif(str_contains(strtolower($log->action), 'logout')) bg-gray-100 text-gray-800
 @else bg-yellow-100 text-yellow-800
 @endif">
 {{ $log->action }}
 </span>
 </td>
 <td class="px-6 py-4 text-gray-600">
 @if($log->entity_type)
 {{ $log->entity_type }}
 @if($log->entity_id)
 <span class="text-gray-400">#{{ $log->entity_id }}</span>
 @endif
 @else
 -
 @endif
 </td>
 <td class="px-6 py-4 text-gray-600">
 @if($log->changes)
 <button onclick="showDetails({{ json_encode($log->changes) }})" class="text-primary-600 hover:text-primary-800 text-sm">
 <i class="fas fa-eye mr-1"></i>View Details
 </button>
 @else
 -
 @endif
 </td>
 <td class="px-6 py-4 text-gray-600 text-sm">{{ $log->ip_address ?? '-' }}</td>
 <td class="px-6 py-4 text-gray-600 text-sm">{{ $log->created_at->format('d M Y, h:i A') }}</td>
 </tr>
 @empty
 <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No activity logs found</td></tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class="mt-6">{{ $activityLogs->links() }}</div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center overflow-y-auto">
 <div class="bg-white rounded-xl p-6 max-w-lg w-full mx-4 my-8 max-h-[90vh] overflow-y-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-xl font-bold text-gray-800">Activity Details</h3>
 <button onclick="closeDetails()" class="text-gray-500 hover:text-gray-700">
 <i class="fas fa-times text-xl"></i>
 </button>
 </div>
 <pre id="detailsContent" class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto"></pre>
 </div>
</div>

<script>
function showDetails(changes) {
 document.getElementById('detailsContent').textContent = JSON.stringify(changes, null, 2);
 document.getElementById('detailsModal').classList.remove('hidden');
}
function closeDetails() {
 document.getElementById('detailsModal').classList.add('hidden');
}
document.getElementById('detailsModal').addEventListener('click', function(e) {
 if (e.target === this) closeDetails();
});
</script>
@endsection
