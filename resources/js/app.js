import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import '../css/app.css';
import Swal from 'sweetalert2';

import 'select2';
import 'select2/dist/css/select2.min.css';

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;
window.Swal = Swal;

(function () {
    if (typeof window.$ === 'undefined') return;
    const $ = window.$;

    function getNestedValue(data, path) {
        return path.split('.').reduce((obj, key) => (obj && obj[key] !== undefined) ? obj[key] : null, data);
    }

    function populateDetailModal(modal, data) {
        modal.find('[data-field]').each(function () {
            const $el = $(this);
            const fieldPath = $el.data('field');             
            const val = getNestedValue(data, fieldPath); 
            
            
            if (fieldPath === 'status') {
                $el.text(val);
                $el.removeClass('bg-success bg-danger');
                if (val === 'aktif') {
                    $el.addClass('bg-success');
                } else if (val === 'nonaktif') {
                    $el.addClass('bg-danger');
                } else {
                    $el.text('');
                }
            } else if (fieldPath.includes('photo')) {
                if (val) {
                    $el.attr('src', '/' + val);
                    $el.removeClass('d-none');
                } else {
                    $el.attr('src', '/icons/no-image.png');
                }
            } else {
                if ($el.is("img")) {
                    if (val) {
                        $el.attr("src", "/" + val);
                    } else {
                        $el.attr("src", "");
                    }
                }else{
                    const displayText = (val === undefined || val === null) ? '' : val;
                    $el.text(displayText);
                }
            }
        });
    }

    $(document).on('click', '[data-action="detail"]', function () {
            const resource = $(this).data('resource');
            const id = $(this).data('id');
            const target = $(this).data('target') || '#modalDetail';
            if (!resource || !id) return;

            $.get(`${resource}/${id}`)
                .done(function (res) {
                    if (res?.code === 200 && res.data) {
                        const modalEl = document.querySelector(target);
                        if (!modalEl) return;
                        const modal = $(modalEl);

                        populateDetailModal(modal, res.data);
                        new bootstrap.Modal(modalEl).show();
                    } else {
                        Swal.fire('Oops!', res?.message || 'Gagal mengambil data', 'warning');
                    }
                })
                .fail(() => Swal.fire('Oops!', 'Gagal mengambil data detail', 'error'));
        });

    function populateForm(form, data) {
        Object.keys(data).forEach((key) => {
            const val = data[key];
            const elems = form.find(`[name="${key}"]`);
            if (!elems.length) return;

            elems.each(function () {
                const $this = $(this);
                const type = $this.attr('type');

                if (type === 'radio') {
                    if ($this.val() == val) $this.prop('checked', true);
                } else if (type === 'checkbox') {
                    if (Array.isArray(val)) {
                        $this.prop('checked', val.includes($this.val()));
                    } else {
                        $this.prop('checked', !!val);
                    }
                } else {
                    $this.val(val);
                    if ($this.hasClass('select2-hidden-accessible')) {
                        $this.trigger('change');
                    }
                }
            });
        });
    }

    function resetForm(form) {
        form[0]?.reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        form.find('input[type="hidden"][name="_method"]').remove();
    }

    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#sidebarToggle').on('click', function () {
            $('#sidebar').toggleClass('sidebar-expanded sidebar-collapsed');
            $('#contentWrapper').toggleClass('content-expanded content-collapsed');
        });

        document.querySelectorAll('.modal').forEach(modalEl => {
            modalEl.addEventListener('show.bs.modal', function () {
                const modal = $(this);
                modal.find('.invalid-feedback').remove();
                modal.find('.is-invalid').removeClass('is-invalid');
            });
        });

        if ($.fn.DataTable) {
            $('.datatable').each(function () {
                $(this).DataTable({
                    responsive: true,
                    lengthChange: true,
                    pageLength: 10,
                    language: { paginate: { previous: '‹', next: '›' } },
                    columnDefs: [{ orderable: false, targets: -1 }]
                });
            });
        }

        if ($.fn.select2) {
            $('.select2').select2({
                dropdownParent: $('#modal, #modalDetail')
            });
        }

        $(document).on('click', '[data-action="form"]', function () {
            const mode = $(this).data('mode') || 'add';
            const resource = $(this).data('resource');
            const id = $(this).data('id');
            const target = $(this).data('target') || '#modal';
            
            if (!resource) return;

            const modalEl = document.querySelector(target);
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            const form = $(modalEl).find('form');

            const actionUrl = `/${resource}${mode === 'edit' ? '/' + id : ''}`;

            resetForm(form);
            form.attr('action', actionUrl);
            $(modalEl).find('#modalLabel').text(mode === 'add' ? 'Tambah' : 'Edit');

            if (mode === 'edit') {
                form.append('<input type="hidden" name="_method" value="PUT">');
                if (id) {
                    $.get(`/${resource}/${id}`)
                        .done(function (res) {
                            if (res?.code === 200 && res.data) {
                                populateForm(form, res.data);
                                modal.show();
                            } else {
                                Swal.fire('Oops!', res?.message || 'Gagal mengambil data', 'warning');
                            }
                        })
                        .fail(() => Swal.fire('Oops!', 'Gagal mengambil data untuk edit', 'error'));
                }
            } else {
                modal.show();
            }
        });

        $(document).on('submit', '.form_submit', function (e) {
            e.preventDefault();
            const $form = $(this);
            const btn = $form.find('[type="submit"], #button_submit');

            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah anda yakin ingin menyimpan data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya!',
                cancelButtonText: 'Tidak!',
            }).then((result) => {
                if (!result.isConfirmed) return;
                
                btn.attr('disabled', true);
                
                const modalLoading = new bootstrap.Modal(document.getElementById('modal_loading'));
                modalLoading.show();

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.find('input[name="_method"]').val() === 'PUT' ? 'POST' : $form.attr('method') || 'POST',
                    data: $form.serialize(),
                    
                    complete: function() {
                        setTimeout(() => modalLoading.hide(), 500);
                        console.log('test');
                        btn.removeAttr('disabled');
                    },
                    
                    success: function(response) {
                        if (response.code === 200) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Oops!', response.message || 'Gagal menyimpan data', 'warning');
                        }
                        document.querySelectorAll('.modal:not(#modal_loading)').forEach(modalEl => {
                            const bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (bsModal) bsModal.hide();
                        });
                    },
                    error: function(jqXHR) {
                        if (jqXHR.status === 400 && Array.isArray(jqXHR.responseJSON?.errors)) {
                            const errorsArray = jqXHR.responseJSON.errors;
                            const responseMessage = jqXHR.responseJSON.message || 'Validasi gagal';
        
                            $form.find('.invalid-feedback').remove();
                            $form.find('.is-invalid').removeClass('is-invalid');
        
                            errorsArray.forEach(function (errorObj) {
                                const field = errorObj.field;
                                const message = errorObj.message;
                                const elem = $form.find(`[name="${field}"]`);
                                if (!elem.length) return;
                                const errorMessage = `<span class="d-flex text-danger invalid-feedback">${message}</span>`;
                                if (elem.parent().hasClass('input-group')) {
                                    elem.parent().children("input:last-child").addClass('is-invalid');
                                    elem.parent().append(errorMessage);
                                } else {
                                    elem.addClass('is-invalid');
                                    elem.after(errorMessage);
                                }
                            });
                            
                            Swal.fire('Oops!', responseMessage, 'warning');
                            
                        } else if (jqXHR.status === 500) {
                            Swal.fire('Error!', jqXHR.responseJSON?.message || 'Server error', 'error');
                        } else {
                            Swal.fire('Oops!', 'Something wrong, try again later', 'error');
                        }
                    }
                });
            });
        });

        $(document).on('click', '[data-action="delete"]', function () {
            const resource = $(this).data('resource');
            const id = $(this).data('id');
            if (!resource || !id) return;
            Swal.fire({
                title: 'Yakin?',
                text: 'Data ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/${resource}/${id}`,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                    success: function (response) {
                        if (response.code === 200) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Oops!', response.message || 'Gagal menghapus', 'warning');
                        }
                    },
                    error: function () {
                        Swal.fire('Oops!', 'Gagal menghapus data', 'error');
                    }
                });
            });
        });
    });

    //? Auth
    $('#form_login').submit(function (e) {
        e.preventDefault();

        const modalLoading = new bootstrap.Modal(document.getElementById('modal_loading'));
        modalLoading.show();

        $.ajax({
            url: "/login",
            type: "POST",
            data: $('#form_login').serialize(),
            dataType: "JSON",
            success: function (response) {
                setTimeout(() => modalLoading.hide(), 500);

                if (response.code === 201) {
                    Swal.fire('Berhasil!', response.message, 'success').then(function(){
                        window.location.href = response.link;
                    })
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
                setTimeout(() => modalLoading.hide(), 500);
                if(jqXHR.responseJSON.message){
                    Swal.fire("Oops!", jqXHR.responseJSON.message, 'error');
                } else{
                    Swal.fire(
                        'Oops!',
                        'Terjadi kesalahan segera hubungi tim IT (' + errorThrown + ')',
                        'error'
                    );
                }
            }
        });
    });

    $('#form_register').submit(function (e) {
        e.preventDefault();

        const modalLoading = new bootstrap.Modal(document.getElementById('modal_loading'));
        modalLoading.show();

        $.ajax({
            url: "/register",
            type: "POST",
            data: $(this).serialize(),
            dataType: "JSON",

            success: function (response) {
                setTimeout(() => modalLoading.hide(), 500);

                if (response.code === 201) {
                    Swal.fire('Berhasil!', response.message, 'success').then(function(){
                        window.location.href = response.link;
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },

            error: function (jqXHR, textStatus, errorThrown) {
                setTimeout(() => modalLoading.hide(), 500);
                Swal.fire('Oops!', 'Terjadi kesalahan (' + errorThrown + ')', 'error');
            }
        });
    });

})();