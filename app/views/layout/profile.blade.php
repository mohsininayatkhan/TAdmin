<div class="edit_profile floating_box popup">

    <div class="container">

        <div class="pad">

            <header>
               <h2 class="what">Edit your details</h2>
            </header>

            <section>
                <form action="" id="frmProfile" method="post">

                    <div class="row wrap_inp">
                        <label for="name" class="caption">Name:</label>
                        <span class="text"><input type="text" id="name"  name="name" class="inp" /></span>
                    </div>

                    <div class="row wrap_inp">
                        <label for="email" class="caption">Email:</label>
                        <span class="text"><input type="text" id="email"  name="email" class="inp" /></span>
                    </div>

                    <div class="row wrap_inp">
                        <label for="status" class="caption">Custom status:</label>
                        <span class="text"><input type="text" id="status" name="status" class="inp" /></span>
                    </div>
					
					<em class="note block">(Leave the following fields empty if you do not want to change it.)</em>
					
					<div class="row wrap_inp">
                        <label for="password" class="caption">Current password:</label>
                        <span class="text"><input type="password" id="password" name="password" class="inp" /></span>
                    </div>
					
					<div class="row wrap_inp">
                        <label for="password_new" class="caption">New password:</label>
                        <span class="text"><input type="password" id="password_new" name="password_new" class="inp" /></span>
                    </div>
					
					<div class="row wrap_inp">
                        <label for="password_confirm" class="caption">Confirm password:</label>
                        <span class="text"><input type="password" id="password_confirm" name="password_confirm" class="inp" /></span>
                    </div>
					
					<div class="row wrap_inp wrap_file">
                        <label for="photo" class="caption">Photo:</label>
                       <input type="file" id="photo" name="photo" />
                    </div>

                    <div class="row">
                        <span class="button">
                            <input type="submit" value="Save" />
                            <input type="button" value="Cancel" class="gray" onclick="hideProfile()" />
                        </span>
                    </div>

                </form>
            </section>
        </div>

        <span class="ie_shadow_top"></span>
        <span class="ie_shadow_bottom"></span>
        <span class="ie_shadow_left"></span>
        <span class="ie_shadow_right"></span>
    </div>
	
</div>
<div class="overlay popup"></div>