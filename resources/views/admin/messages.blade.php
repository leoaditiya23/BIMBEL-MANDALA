@extends('admin.dashboard_admin')

@section('admin_content')
<div class="w-full pb-20 relative z-10" x-data="{ openModal: false, activeMsg: '', activeId: '' }">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Pesan Masuk</h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">Kelola pesan dan pertanyaan dari calon siswa.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- TOMBOL KELOLA FAQ (ARSIP) --}}
            <a href="{{ route('admin.faqs') }}" class="flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 px-5 py-3 rounded-2xl transition-all border border-slate-200 group">
                <i class="fas fa-question-circle text-slate-500 group-hover:text-blue-600 transition-colors"></i>
                <span class="font-bold text-slate-600 text-sm">Kelola FAQ</span>
            </a>

            <div class="flex items-center space-x-3 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
                <i class="fas fa-inbox text-orange-500"></i>
                <span class="font-bold text-slate-700">{{ $messages->count() }} Total Pesan</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center animate-bounce">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Pengirim</th>
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Isi Pesan</th>
                        <th class="px-8 py-6 text-left text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Tanggal</th>
                        <th class="px-8 py-6 text-center text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                                        {{ substr($msg->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700">{{ $msg->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-medium">{{ $msg->email ?? 'Tanpa Email' }}</span>
                                        @if($msg->whatsapp)
                                            <span class="text-[10px] text-green-500 font-bold flex items-center mt-0.5">
                                                <i class="fab fa-whatsapp mr-1"></i> {{ $msg->whatsapp }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="max-w-md">
                                    <p class="text-slate-600 leading-relaxed">{{ $msg->message }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm text-slate-400 font-medium">
                                {{ $msg->created_at->diffForHumans() }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- TOMBOL BALAS WA --}}
                                    @if($msg->whatsapp)
                                        @php
                                            $phone = preg_replace('/[^0-9]/', '', $msg->whatsapp);
                                            if (str_starts_with($phone, '0')) {
                                                $phone = '62' . substr($phone, 1);
                                            }
                                            $waText = urlencode("Halo " . $msg->name . ", kami dari Mandala Bimbel ingin menanggapi pesan Anda: \"" . $msg->message . "\"");
                                        @endphp
                                        <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank"
                                            class="w-10 h-10 bg-white border border-slate-100 text-green-500 rounded-xl hover:bg-green-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i class="fab fa-whatsapp text-lg"></i>
                                        </a>
                                    @endif

                                    {{-- TOMBOL FAQ: Membuka Modal --}}
                                    <button type="button" 
                                            onclick="openFaqModal('{{ $msg->id }}', '{{ addslashes($msg->message) }}')"
                                            class="w-10 h-10 bg-white border border-slate-100 text-blue-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="fas fa-question text-sm"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.messages.delete', $msg->id) }}" method="POST" onsubmit="return confirm('Hapus pesan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 bg-white border border-slate-100 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium italic">Tidak ada pesan masuk saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="faqModal" class="hidden fixed inset-0 z-[99] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-slate-800">Jadikan FAQ</h3>
                    <button onclick="closeFaqModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>

                <form action="{{ route('admin.faq.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="message_id" id="modal_msg_id">
                    
                    <div class="mb-5">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pertanyaan (Dari Pesan)</label>
                        <textarea name="question" id="modal_question" rows="3" 
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-slate-700 font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Jawaban Anda</label>
                        <textarea name="answer" rows="4" required placeholder="Tulis jawaban resmi bimbingan belajar di sini..."
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-slate-700 font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all"></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeFaqModal()" class="flex-1 px-6 py-4 rounded-2xl font-bold text-slate-500 hover:bg-slate-100 transition-all">Batal</button>
                        <button type="submit" class="flex-1 px-6 py-4 rounded-2xl font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">Simpan ke FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openFaqModal(id, message) {
        document.getElementById('modal_msg_id').value = id;
        document.getElementById('modal_question').value = message;
        document.getElementById('faqModal').classList.remove('hidden');
    }

    function closeFaqModal() {
        document.getElementById('faqModal').classList.add('hidden');
    }
</script>
@endsection