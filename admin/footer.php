        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>
<script>
(function () {
    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('adminSidebarBackdrop');
    var toggle = document.getElementById('adminMenuToggle');

    function setOpen(open) {
        if (!sidebar || !backdrop || !toggle) return;
        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-visible', open);
        backdrop.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            setOpen(!sidebar.classList.contains('is-open'));
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            setOpen(false);
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            setOpen(false);
        }
    });

    if (window.jQuery) {
        jQuery(function ($) {
            if ($('#example').length) {
                $('#example').DataTable({
                    order: [],
                    pageLength: 10,
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries'
                    }
                });
            }
        });
    }
})();
</script>
</body>
</html>
