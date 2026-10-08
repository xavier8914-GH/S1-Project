<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

$pageTitle = 'Privacy Policy — PhonePulse';
require __DIR__ . '/header.php';
?>

<article class="policy-page">
  <h1>Privacy Policy</h1>
  <p class="policy-updated">Last updated: September 27, 2026</p>
  <p>This policy explains what PhonePulse collects through the features currently available on this site, why it is used, and the choices you have.</p>

  <h2>1. Information you provide</h2>
  <ul>
    <li><strong>Account details:</strong> your username and a password hash when you create an account. The site stores a hash, not your readable password.</li>
    <li><strong>Comments:</strong> the text you post and the username associated with your account. Comments are visible to visitors on the relevant phone listing.</li>
    <li><strong>Contact messages:</strong> your name, email address, and message when you use Contact Us. Site administrators can view these messages to respond or address the request.</li>
    <li><strong>Favorites:</strong> the phone IDs you save while signed in, associated with your account.</li>
  </ul>

  <h2>2. Information collected when you visit</h2>
  <p>The site uses a session cookie to keep you signed in and protect form submissions. It does not currently use advertising cookies or an analytics service. The hosting provider may process technical information such as IP address, request time, and browser details in server logs to deliver, secure, and troubleshoot the site; the site's hosting and log-retention settings determine how long those logs are kept.</p>

  <h2>3. How information is used</h2>
  <p>We use the information above to operate accounts, display comments, save favorites, receive and respond to contact messages, protect the site against abuse, and maintain the service. We do not use contact details for marketing through the features currently provided.</p>

  <h2>4. Public content and sharing</h2>
  <p>Your comments and account username are shown publicly next to the relevant phone. Do not post information you want to keep private. Contact messages are not displayed publicly, but can be read by administrators who manage the site.</p>
  <p>PhonePulse does not sell your personal information. Information may be processed by the hosting or infrastructure providers needed to run the site, or disclosed when required by law or reasonably necessary to protect users, the site, or another person's rights. External links open third-party websites; their privacy policies apply there.</p>

  <h2>5. Retention and deletion requests</h2>
  <p>Account, favorite, and comment records are kept in the site's database while needed to provide the features and maintain site security. Contact messages remain available to administrators until they are deleted from the site's database. Backups or security records may persist for a limited period according to the hosting setup.</p>
  <p>You may request access to, correction of, or deletion of information associated with you by using <a href="<?= e(app_url('contact.php')) ?>">Contact Us</a>. We may need to verify your request and may retain information where required by law or for legitimate security purposes. Removing an account may also remove associated favorites; public comments may be removed or de-identified where appropriate.</p>

  <h2>6. Security</h2>
  <p>PhonePulse uses measures such as password hashing, prepared database queries, and form protections to reduce risk. No website or method of storage is completely secure, so we cannot promise absolute security. Contact us promptly if you suspect an account or personal information has been exposed.</p>

  <h2>7. Children and international visitors</h2>
  <p>The site is not designed to collect personal information from children who are not permitted to use online services under the laws that apply to them. If you believe a child has submitted information, contact us. The site may be accessed from different countries; hosting location and applicable privacy rights can depend on the service configuration and your location.</p>

  <h2>8. Changes to this policy</h2>
  <p>We may update this policy as the site changes or legal requirements evolve. The latest version and its effective date will be posted on this page.</p>

  <h2>9. Contact</h2>
  <p>For privacy questions or requests, use our <a href="<?= e(app_url('contact.php')) ?>">Contact Us</a> page and describe the information or request you are asking about.</p>
</article>

<?php require __DIR__ . '/footer.php'; ?>

