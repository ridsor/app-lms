{{-- <div class="toastr-message" data-type="success">{{ session('success') }}</div> --}}
<div class="toasts common-toasts" style="z-index: 10000;">
    <div data-type="success" class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast" id="toast-success" role="alert" aria-live="polite" aria-atomic="true">
            <div class="common-space alert-light-success">
                <div class="toast-body"><i class="close-search stroke-success" data-feather="check-square"></i><span
                        id="toast-text"></span></div><button class="btn-close" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast" id="toast-error" role="alert" aria-live="polite" aria-atomic="true">
            <div class="common-space alert-light-danger">
                <div class="toast-body">
                    <i class="close-search stroke-danger" data-feather="x-circle"></i>
                    <span id="toast-text"></span>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div data-type="info" class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast" id="toast-info" role="alert" aria-live="polite" aria-atomic="true">
            <div class="common-space alert-light-info">
                <div class="toast-body"><i class="close-search stroke-info" data-feather="info"></i><span
                        id="toast-text"></span>
                </div><button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div data-type="warning" class="toast-container position-fixed bottom-0 end-0 p-3 toast-index toast-rtl">
        <div class="toast" id="toast-warning" role="alert" aria-live="polite" aria-atomic="true">
            <div class="common-space alert-light-warning">
                <div class="toast-body"><i class="close-search stroke-warning" data-feather="alert-circle"></i><span
                        id="toast-text"></span></div><button class="btn-close" type="button" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
{{-- 
<script>
    $(document).ready(function() {
        $('.toastr-message').each(function() {
            const toast = new bootstrap.Toast(this);
            toast.show();
        });
    });
</script> --}}
