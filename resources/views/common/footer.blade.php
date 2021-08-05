<footer>
    <div class="copyright text-center">
        <p class="mt-5 mb-5 text-muted">&copy; 2021 Fresns</p>
    </div>
</footer>

<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/jquery-3.6.0.min.js"></script>
<script>
    /* Bootstrap Tooltips */
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>

<script>
    $(function () {
        $(document).ready(function(){
            var val = window.location.search;
            if(val){
                var text = $('#navbarContent .dropdown-item[href="'+val+'"]').text();
                $('#language').find('span').text(text)
            }
            $("#navbarContent .dropdown-item").click(function(){
                var url = $(this).attr('href');
                $.ajax({
                    url: '/fresns/setLanguage',
                    type: 'post',
                    data: {'lang':url},
                    dataType: 'json',
                    success: function (resp) {
                        // console.log(file);
                        if(resp.code == 0){
                            window.location.reload();
                        }
                    }
                })
            })
            function getQueryVariable(variable)
            {
                var query = window.location.search.substring(1);
                var vars = query.split("&");
                for (var i=0;i<vars.length;i++) {
                    var pair = vars[i].split("=");
                    if(pair[0] == variable){return pair[1];}
                }
                return(false);
            }
        })
    })
</script>
