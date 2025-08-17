<!-- Validator -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Validator</h2>
    <!-- Email requis avec hint -->
    <div>
        <input class="input validator" type="email" required placeholder="[email protected]" />
        <div class="validator-hint">Enter valid email address</div>
    </div>

    <!-- Mot de passe (pattern) -->
    <div>
        <input type="password" class="input validator" required placeholder="Password" minlength="8"
            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
            title="Must be more than 8 characters, including number, lowercase letter, uppercase letter" />
        <p class="validator-hint">Must be more than 8 characters, including<br/>number, lowercase, uppercase</p>
    </div>

    <!-- Username -->
    <div>
        <input type="text" class="input validator" required placeholder="Username"
            pattern="[A-Za-z][A-Za-z0-9\-]*" minlength="3" maxlength="30" title="Only letters, numbers or dash" />
        <p class="validator-hint">3-30 chars, letters, numbers or dash</p>
    </div>

    <!-- Select requis -->
    <form class="space-y-2">
        <select class="select validator" required>
            <option disabled selected value="">Choose:</option>
            <option>Tabs</option>
            <option>Spaces</option>
        </select>
        <p class="validator-hint">Required</p>
        <button class="btn" type="submit">Submit form</button>
    </form>
</section>


