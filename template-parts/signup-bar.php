    <!-- Join bar -->
    <div class="join-bar" id="join-email" role="region" aria-label="Join the cause sign up">
      <div>
        <div class="title">Join the Cause</div>
        <div style="font-size:13px; opacity:0.95;">Get occasional updates & event invites</div>
      </div>

      <form onsubmit="event.preventDefault(); alert('Thanks — you\'re signed up!');" aria-label="Newsletter signup">
        <label for="email" class="sr-only" style="display:none">Email</label>
        <input type="email" id="email" name="email" placeholder="Email address" required />
        <button type="submit">Sign up</button>
      </form>
    </div>