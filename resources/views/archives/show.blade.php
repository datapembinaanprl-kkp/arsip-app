@extends('layouts.app')
@section('title', 'Arsip Dokumen')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Detail Dokumen</h1>
  </div>
</div>



@endsection

@push('scripts')
<script>
function openTolakModal(id, judul) {
  document.getElementById('tolak-doc-name').textContent = 'Dokumen: ' + judul;
  document.getElementById('tolak-form').action = '/archives/' + id + '/tolak';
  const modal = document.getElementById('tolak-modal');
  modal.style.display = 'flex';
}
function closeTolakModal() {
  document.getElementById('tolak-modal').style.display = 'none';
}
document.getElementById('tolak-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeTolakModal();
});
</script>
@endpush