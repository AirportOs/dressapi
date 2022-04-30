<main class="form-sign">
        <form id="signin" method="post" onsubmit="return false">
            <input type="hidden" name="do" value="login">
            <img class="mb-4" src="/apple-touch-icon-72x72.png" alt="" width="72" height="72">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

            <div class="form-floating">
            <input type="text" name="dusername" class="form-control" id="floatingInput" placeholder="admin">
            <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
            <input type="password" name="dpassword" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
            </div>

            <button id="signin_but" class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2020-2022</p>
        </form>
    </main>
