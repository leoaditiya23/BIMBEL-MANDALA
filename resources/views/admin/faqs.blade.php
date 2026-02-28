@extends('admin.dashboard_admin')

@section('admin_content')
<div class="w-full pb-20 relative z-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Manajemen FAQ</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Edit atau hapus pertanyaan yang sudah dipublish ke publik.</p>
        </div>
        <div class="flex items-center space-x-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
            <i class="fas fa-question-circle text-blue-500"></i>
            <span class="font-bold text-slate-700">{{ $faqs->count() }} FAQ Terbit</span>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center animate-pulse">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        @forelse($faqs as $faq)
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full">Pertanyaan</span>
                        <h4 class="text-lg font-bold text-slate-800 mt-2">{{ $faq->question }}</h4>
                        
                        <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Jawaban:</span>
                            <p class="text-slate-600 leading-relaxed">{{ $faq->answer }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button" 
                            onclick="openEditFaq('{{ $faq->id }}', '{{ addslashes($faq->question) }}', '{{ addslashes($faq->answer) }}')"
                            class="w-11 h-11 bg-blue-50 text-blue-500 rounded-2xl hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('admin.faq.delete', $faq->id) }}" method="POST" onsubmit="return confirm('Hapus FAQ ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-11 h-11 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] p-20 text-center border-2 border-dashed border-slate-100">
                <h3 class="text-xl font-bold text-slate-700">Belum ada FAQ</h3>
                <a href="{{ route('admin.messages') }}" class="text-blue-600 font-bold hover:underline italic">Kembali ke Pesan Masuk →</a>
            </div>
        @endforelse
    </div>

    {{-- MODAL EDIT FAQ --}}
<div id="editFaqModal" class="hidden fixed inset-0 z-[99] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800">Edit FAQ</h3>
                <button type="button" onclick="closeEditFaq()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>

            {{-- UPDATE: Tambahkan ID dan biarkan action kosong agar diisi JS secara dinamis --}}
            <form id="editFaqForm" method="POST">
                @csrf 
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pertanyaan</label>
                    <textarea name="question" id="edit_question" rows="3" required
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Jawaban</label>
                    <textarea name="answer" id="edit_answer" rows="4" required
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeEditFaq()" class="flex-1 px-6 py-4 rounded-2xl font-bold text-slate-500 hover:bg-slate-100 transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-4 rounded-2xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditFaq(id, question, answer) {
        const form = document.getElementById('editFaqForm');
        
        // UPDATE: Gunakan rute Laravel yang benar. 
        // Kita buat base URL-nya dulu, lalu tempelkan ID di belakangnya.
        // Ini mencegah browser salah mengira ini adalah request GET biasa.
        const baseUrl = "{{ url('admin/faqs/update') }}";
        form.action = baseUrl + '/' + id;
        
        document.getElementById('edit_question').value = question;
        document.getElementById('edit_answer').value = answer;
        
        const modal = document.getElementById('editFaqModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Tambahan: Log untuk memastikan action form sudah berubah (cek di F12 Console)
        console.log("Action form diset ke: " + form.action);
    }

    function closeEditFaq() {
        const modal = document.getElementById('editFaqModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.onclick = function(event) {
        const modal = document.getElementById('editFaqModal');
        if (event.target == modal) {
            closeEditFaq();
        }
    }
</script>
@endsection