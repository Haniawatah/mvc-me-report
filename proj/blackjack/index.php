<?php
// $stateVar, $profileVar, $betsVar, $errorsVar, $statsVar, $lastNetVar, $bankEmptyVar, $route, $baseVar provided by controller
require __DIR__ . '/../partials/header.php';
$state = $stateVar;
$profile = $profileVar ?? null;
$bets = $betsVar ?? [];
$errors = $errorsVar ?? [];
$stats = $statsVar ?? ['rounds'=>0,'wins'=>0,'losses'=>0,'pushes'=>0,'bj'=>0,'busts'=>0,'wagered'=>0,'returned'=>0,'net'=>0];
$lastNet = $lastNetVar ?? null;
$bankEmpty = (bool)($bankEmptyVar ?? false);

// Ensure public CSS on server
if (isset($baseVar)) {
    echo '<link rel="stylesheet" href="' . htmlspecialchars($baseVar) . '/css/style.css">';
}
?>
<script>
(function () {
  var base = <?= json_encode($baseVar ?? '') ?>;
  if (!base) return;
  function needsFix(url) { return typeof url === 'string' && url.startsWith('/') && !url.startsWith('/~'); }
  function fix(url) { return needsFix(url) ? base.replace(/\/$/, '') + url : url; }
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('a[href^="/"]').forEach(function (a) { a.setAttribute('href', fix(a.getAttribute('href'))); });
    document.querySelectorAll('form[action^="/"]').forEach(function (f) { f.setAttribute('action', fix(f.getAttribute('action'))); });
    
    // Save scroll position before form submit
    document.querySelectorAll('form').forEach(function(form) {
      form.addEventListener('submit', function() {
        sessionStorage.setItem('blackjack_scroll', window.scrollY.toString());
      });
    });
    
    // Restore scroll position after page load
    var savedScroll = sessionStorage.getItem('blackjack_scroll');
    if (savedScroll) {
      window.scrollTo(0, parseInt(savedScroll));
      sessionStorage.removeItem('blackjack_scroll');
    }
    
    // Scroll to active hand if exists
    var activeHand = document.querySelector('.active-hand');
    if (activeHand && savedScroll) {
      setTimeout(function() {
        activeHand.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 100);
    }
  });
})();
</script>

<main class="container blackjack-page">
  <section class="blackjack-hero">
    <h1>Black Jack</h1>
    <p>Spela 1–3 händer mot banken. Blackjack betalar 3:2, vinst 1:1, push återbetalas. Insatsen dras vid rundstart och utbetalning sker vid slutsummering.</p>
  </section>

  <?php if (!empty($errors)): ?>
    <div class="flash-error" style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px;padding:.75rem 1rem;margin-bottom:1rem">
      <?php foreach ($errors as $e): ?>
        <div><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!$profile): ?>
    <!-- Profile menu -->
    <section class="blackjack-panel blackjack-start">
      <h2 style="margin:0 0 .5rem;font-size:1rem;letter-spacing:.3px">Starta med spelarnamn</h2>h2>
      <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" class="blackjack-form">
        <label for="player_name">Spelarnamn</label>
        <input id="player_name" name="player_name" type="text" required
               style="background:var(--bj-panel);border:1px solid var(--bj-border);color:var(--bj-text);padding:.55rem .6rem;border-radius:6px;font-size:.9rem" />
        <label for="bank">Startsaldo (kr)</label>
        <input id="bank" name="bank" type="number" min="1" value="100"
               style="background:var(--bj-panel);border:1px solid var(--bj-border);color:var(--bj-text);padding:.55rem .6rem;border-radius:6px;font-size:.9rem" />
        <button class="btn" type="submit" name="action" value="save_profile">Spara & Börja</button>
      </form>
    </section>

  <?php elseif ($state === null): ?>
    <!-- Betting/start round -->
    <section class="blackjack-panel blackjack-start">
      <div class="bank-bar <?= $bankEmpty ? 'bank-empty' : '' ?>">
        <span class="bank-user">Spelare: <strong><?= htmlspecialchars($profile['name'] ?? '') ?></strong></span>
        <span class="bank-balance">Saldo: <strong><?= (int)($profile['bank'] ?? 0) ?> kr</strong></span>
        <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" style="margin-left:auto">
          <button class="btn" type="submit" name="action" value="reset_profile">Återställ till meny</button>
        </form>
      </div>

      <?php if (!$bankEmpty): ?>
      <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" class="blackjack-form">
        <label for="hands">Antal händer</label>
        <select id="hands" name="hands">
          <option value="1">1</option><option value="2">2</option><option value="3">3</option>
        </select>

        <div class="bets-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.6rem">
          <div>
            <label for="bet0">Insats Hand 1</label>
            <input id="bet0" name="bets[0]" type="number" min="1" value="<?= (int)($bets[0] ?? 10) ?>"
                   style="background:var(--bj-panel);border:1px solid var(--bj-border);color:var(--bj-text);padding:.5rem;border-radius:6px" />
          </div>
          <div>
            <label for="bet1">Insats Hand 2</label>
            <input id="bet1" name="bets[1]" type="number" min="1" value="<?= (int)($bets[1] ?? 10) ?>"
                   style="background:var(--bj-panel);border:1px solid var(--bj-border);color:var(--bj-text);padding:.5rem;border-radius:6px" />
          </div>
          <div>
            <label for="bet2">Insats Hand 3</label>
            <input id="bet2" name="bets[2]" type="number" min="1" value="<?= (int)($bets[2] ?? 10) ?>"
                   style="background:var(--bj-panel);border:1px solid var(--bj-border);color:var(--bj-text);padding:.5rem;border-radius:6px" />
          </div>
        </div>

        <button class="btn" type="submit" name="action" value="start">Starta spel</button>
      </form>
      <?php endif; ?>
    </section>

  <?php else: ?>
    <!-- Top bank + hands selector + reset when empty -->
    <div class="bank-bar <?= $bankEmpty ? 'bank-empty' : '' ?>">
      <span class="bank-user">Spelare: <strong><?= htmlspecialchars($profile['name'] ?? '') ?></strong></span>
      <span class="bank-balance">Saldo: <strong><?= (int)($profile['bank'] ?? 0) ?> kr</strong></span>
      <?php
        $currentHands = isset($state['players']) ? max(1, min(3, (int) count($state['players']))) : 1;
      ?>
      <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" class="blackjack-form" style="margin-left:auto;display:flex;gap:.5rem;align-items:center">
        <label for="hands-top" style="font-size:.8rem;color:var(--bj-muted)">Händer:</label>
        <select id="hands-top" name="hands" style="background:var(--bj-panel);color:var(--bj-text);border:1px solid var(--bj-border);border-radius:6px;padding:.25rem .4rem;font-size:.85rem;">
          <option value="1" <?= $currentHands===1?'selected':''; ?>>1</option>
          <option value="2" <?= $currentHands===2?'selected':''; ?>>2</option>
          <option value="3" <?= $currentHands===3?'selected':''; ?>>3</option>
        </select>
        <button class="btn" type="submit" name="action" value="set_hands" <?= $bankEmpty ? 'disabled' : '' ?>>Uppdatera</button>
      </form>
      <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" style="margin-left:.5rem">
        <button class="btn" type="submit" name="action" value="reset_profile">Återställ till meny</button>
      </form>
    </div>

    <!-- Status + Stats -->
    <div class="blackjack-statusbar">
      <div class="status-col">
        <?php if ($state['finished']): ?>
          <span class="status finished">Runda avslutad</span>
        <?php else: ?>
          <span class="status playing">Aktiv hand: <?= $state['current'] + 1 ?></span>
        <?php endif; ?>
      </div>
      <div class="status-col">
        <span class="actions-hint">Actions: <?= implode(', ', $state['actions']) ?></span>
      </div>
      <div class="status-col score-summary">
        <span>Dealer: <?= $state['finished'] ? (int)$state['dealer']['score'] : '??' ?></span>
      </div>
    </div>

    <!-- Stats panel with money aggregates -->
    <section class="blackjack-panel stats-panel">
      <h2 style="margin:0;font-size:.95rem;letter-spacing:.3px;text-transform:uppercase">Spelstatistik</h2>
      <?php
        $total = max(1,(int)$stats['rounds']);
        $winrate = number_format((($stats['wins'] ?? 0)/$total)*100, 1);
      ?>
      <div class="stats-grid">
        <div class="stat-item"><span class="stat-label">Rundor</span><span class="stat-value"><?= (int)$stats['rounds'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Vinster</span><span class="stat-value"><?= (int)$stats['wins'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Förluster</span><span class="stat-value"><?= (int)$stats['losses'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Push</span><span class="stat-value"><?= (int)$stats['pushes'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Blackjacks</span><span class="stat-value"><?= (int)$stats['bj'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Bust</span><span class="stat-value"><?= (int)$stats['busts'] ?></span></div>
        <div class="stat-item"><span class="stat-label">Winrate</span><span class="stat-value"><?= $winrate ?>%</span></div>
        <div class="stat-item"><span class="stat-label">Omsättning</span><span class="stat-value"><?= (int)($stats['wagered'] ?? 0) ?> kr</span></div>
        <div class="stat-item"><span class="stat-label">Utbetalning</span><span class="stat-value"><?= (int)($stats['returned'] ?? 0) ?> kr</span></div>
        <div class="stat-item"><span class="stat-label">Netto (totalt)</span><span class="stat-value" style="color:<?= (int)($stats['net'] ?? 0) >= 0 ? '#10b981' : '#ef4444' ?>"><?= (int)($stats['net'] ?? 0) ?> kr</span></div>
      </div>
      <?php if ($state['finished'] && $lastNet !== null): ?>
        <p class="score-line" style="margin-top:.4rem">Rundans netto: <strong style="color:<?= $lastNet >= 0 ? '#10b981' : '#ef4444' ?>"><?= $lastNet ?></strong> kr</p>
      <?php endif; ?>
    </section>

    <div class="blackjack-layout">
      <article class="blackjack-panel dealer-panel">
        <h2>Banken</h2>
        <div class="cards-row">
          <?php foreach ($state['dealer']['cards'] as $idx => $c): ?>
            <div style="display:inline-block;position:relative;margin-right:10px">
              <span class="card-token"><?= htmlspecialchars($c) ?></span>
              <span style="position:absolute;top:-8px;left:-8px;background:#f59e0b;color:white;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:bold"><?= $idx + 1 ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <p class="score-line">
          Summa:
          <?php if ($state['finished']): ?>
            <strong><?= (int)$state['dealer']['score'] ?></strong>
          <?php else: ?>
            <em>?</em>
          <?php endif; ?>
        </p>
        
        <?php if ($state['finished'] && !empty($state['dealer']['drawLog'])): ?>
          <div style="margin-top:1rem;padding:.75rem;background:rgba(0,0,0,0.1);border-radius:6px">
            <h3 style="margin:0 0 .5rem;font-size:.85rem;color:var(--bj-muted)">Korthistorik:</h3>
            <ul style="margin:0;padding-left:1.2rem;font-size:.85rem;line-height:1.6">
              <?php foreach ($state['dealer']['drawLog'] as $idx => $log): ?>
                <li><strong>Kort <?= $idx + 1 ?>:</strong> <?= htmlspecialchars($log[1]) ?> <em style="color:var(--bj-muted)">(<?= htmlspecialchars($log[0]) ?>)</em></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </article>

      <div class="hands-grid">
        <?php foreach ($state['players'] as $i => $h): ?>
          <?php
            $bet = (int)($bets[$i] ?? 0);
            $canSplit = !empty($h['canSplit']);
            // disable split if not allowed or saldo insufficient to duplicate bet
            $splitDisabled = (!$canSplit || (int)($profile['bank'] ?? 0) < $bet) ? 'disabled' : '';
            $isBj = !empty($h['bj']) && !empty($h['cards']) && is_array($h['cards']) && count($h['cards']) === 2;
          ?>
          <article class="blackjack-panel hand-panel <?= ($state['current'] === $i && !$state['finished']) ? 'active-hand' : '' ?>">
            <header class="hand-header">
              <h2>Hand <?= $i + 1 ?></h2>
              <span class="hand-score <?= $h['bust'] ? 'bust' : '' ?>">
                <?= (int)$h['score'] ?>
                <?php if ($h['bj']): ?><span class="tag tag-bj">BJ</span><?php endif; ?>
                <?php if ($h['bust']): ?><span class="tag tag-bust">BUST</span><?php endif; ?>
              </span>
            </header>

            <div class="cards-row">
              <?php foreach ($h['cards'] as $c): ?>
                <span class="card-token"><?= htmlspecialchars($c) ?></span>
              <?php endforeach; ?>
            </div>

            <p class="score-line">Insats: <strong><?= $bet ?> kr</strong></p>

            <div class="hand-footer">
              <?php if ($state['finished'] && isset($state['results'][$i])): ?>
                <?php $r = $state['results'][$i]; ?>
                <?php
                  // Show per-hand net, based on same rules as controller
                  if (!empty($r['win'])) {
                      $net = $isBj ? (int)floor($bet * 1.5) : $bet; // returned - bet
                      $msg = $isBj ? "Vinst (BJ +1.5x) +{$net}" : "Vinst +{$net}";
                      $cls = 'win';
                  } elseif (!empty($r['push'])) {
                      $net = 0;
                      $msg = "Push (0)";
                      $cls = 'push';
                  } else {
                      $net = -$bet;
                      $msg = "Förlust ({$net})";
                      $cls = 'lose';
                  }
                  
                  // Get dealer info for comparison
                  $dealerScore = (int)$state['dealer']['score'];
                  $dealerCards = $state['dealer']['cards'];
                  $playerScore = (int)$h['score'];
                ?>
                <p class="result-line">
                  Resultat: <span class="result <?= $cls ?>"><?= $msg ?></span>
                </p>
                
                <div style="margin-top:.75rem;padding:.6rem;background:rgba(0,0,0,0.05);border-radius:4px;font-size:.85rem">
                  <div style="margin-bottom:.3rem">
                    <strong>Din hand:</strong> <?= $playerScore ?> poäng
                    <?php if ($h['bust']): ?>
                      <span style="color:#ef4444;font-weight:bold"> (BUST)</span>
                    <?php elseif ($h['bj']): ?>
                      <span style="color:#f59e0b;font-weight:bold"> (BLACKJACK)</span>
                    <?php endif; ?>
                  </div>
                  <div style="margin-bottom:.3rem">
                    <strong>Bankens hand:</strong> <?= $dealerScore ?> poäng
                    <?php if ($dealerScore > 21): ?>
                      <span style="color:#ef4444;font-weight:bold"> (BUST)</span>
                    <?php endif; ?>
                  </div>
                  <div style="color:var(--bj-muted);font-size:.8rem">
                    Bankens kort: <?= implode(', ', array_map('htmlspecialchars', $dealerCards)) ?>
                  </div>
                  
                  <?php if (!$h['bust']): ?>
                    <div style="margin-top:.4rem;padding-top:.4rem;border-top:1px solid rgba(0,0,0,0.1)">
                      <?php if ($dealerScore > 21): ?>
                        <em>Banken fick bust - du vinner!</em>
                      <?php elseif ($playerScore > $dealerScore): ?>
                        <em>Du hade högre än banken (<?= $playerScore ?> > <?= $dealerScore ?>)</em>
                      <?php elseif ($playerScore === $dealerScore): ?>
                        <em>Samma poäng som banken (<?= $playerScore ?> = <?= $dealerScore ?>)</em>
                      <?php else: ?>
                        <em>Banken hade högre (<?= $dealerScore ?> > <?= $playerScore ?>)</em>
                      <?php endif; ?>
                    </div>
                  <?php else: ?>
                    <div style="margin-top:.4rem;padding-top:.4rem;border-top:1px solid rgba(0,0,0,0.1)">
                      <em>Du fick bust över 21 - automatisk förlust</em>
                    </div>
                  <?php endif; ?>
                </div>
              <?php elseif ($state['current'] === $i): ?>
                <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" class="actions-form">
                  <button class="btn" type="submit" name="action" value="hit">Ta kort</button>
                  <button class="btn" type="submit" name="action" value="stand">Stanna</button>
                  <?php if ($canSplit): ?>
                    <button class="btn" type="submit" name="action" value="split" <?= $splitDisabled ?>>Splitta</button>
                  <?php endif; ?>
                </form>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="blackjack-actions-global">
      <form method="post" action="<?= htmlspecialchars($route ?? '/blackjack') ?>" style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
        <button class="btn new-round" type="submit" name="action" value="new" <?= $bankEmpty ? 'disabled' : '' ?>>Nytt spel</button>
        <button class="btn" type="submit" name="action" value="reset_profile">Återställ till meny</button>
      </form>
    </div>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>