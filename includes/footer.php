</div>

<footer class="text-center mt-5 mb-3 text-muted">

Pizza Shop © 2026

</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const forms = document.querySelectorAll("form");

    forms.forEach(function(form){

        form.addEventListener("submit", function(){

            const button = form.querySelector("button");

            if(button){

                button.disabled = true;

                button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Processing...
                `;

            }

        });

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>