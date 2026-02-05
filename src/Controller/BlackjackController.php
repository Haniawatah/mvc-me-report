<?php

declare(strict_types=1);

namespace App\Controller;

use App\Blackjack\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlackjackController extends AbstractController
{
    #[Route('/blackjack', name: 'blackjack', methods: ['GET','POST'])]
    #[Route('/game21', name: 'game21', methods: ['GET','POST'])]
    public function play(Request $request): Response
    {
        $session = $request->getSession();

        $profile = $session->get('blackjack_profile');
        $errors = [];

        // Save or update player profile
        if ($request->isMethod('POST') && (string)$request->request->get('action') === 'save_profile') {
            $name = trim((string) $request->request->get('player_name', ''));
            $bank = (int) ($request->request->get('bank', 100));
            $bank = max(1, $bank);
            if ($name === '') {
                $errors[] = 'Du måste ange ett spelarnamn.';
            } else {
                $profile = ['name' => $name, 'bank' => $bank];
                $session->set('blackjack_profile', $profile);
                // Clear old game data
                $session->remove('blackjack');
                $session->remove('blackjack_bets');
                $session->remove('blackjack_hands');
                $session->remove('blackjack_settled');
                $session->remove('blackjack_stats');
                $session->remove('blackjack_last_net');
            }
        }

        /** @var Game|null $game */
        $game = $session->get('blackjack');
        if ($game && !($game instanceof Game)) {
            $game = null;
        }

        $storedHands = (int) ($session->get('blackjack_hands') ?? 1);
        $bets = $session->get('blackjack_bets') ?? [];

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action', '');

            // Reset to main menu
            if ($action === 'reset_profile') {
                $session->remove('blackjack_profile');
                $session->remove('blackjack');
                $session->remove('blackjack_bets');
                $session->remove('blackjack_hands');
                $session->remove('blackjack_settled');
                $session->remove('blackjack_stats');
                $session->remove('blackjack_last_net');
                $profile = null;
                $game = null;
            } elseif ($action !== 'save_profile' && !$profile) {
                $errors[] = 'Du måste skapa en profil först.';
            } else {
                if ($action === 'start') {
                    // Start new game
                    $hands = max(1, min(3, (int) ($request->request->get('hands') ?? 1)));
                    $session->set('blackjack_hands', $hands);

                    $incomingBets = (array) $request->request->all('bets');
                    $roundBets = [];
                    $sum = 0;
                    for ($i = 0; $i < $hands; $i++) {
                        $b = isset($incomingBets[$i]) ? (int) $incomingBets[$i] : 1;
                        $b = max(1, $b);
                        $roundBets[$i] = $b;
                        $sum += $b;
                    }

                    if ($profile['bank'] < $sum) {
                        $errors[] = 'Du har inte råd med de insatserna.';
                    } else {
                        $profile['bank'] -= $sum;
                        $session->set('blackjack_profile', $profile);
                        $session->set('blackjack_bets', $roundBets);
                        $session->set('blackjack_settled', false);
                        $game = new Game($hands);
                        $session->set('blackjack', $game);
                    }
                } elseif ($action === 'set_hands') {
                    // Change number of hands
                    $hands = max(1, min(3, (int) ($request->request->get('hands') ?? 1)));
                    $session->set('blackjack_hands', $hands);

                    $defaultBet = 1;
                    $incomingBets = (array) $request->request->all('bets');
                    $roundBets = [];
                    $sum = 0;
                    for ($i = 0; $i < $hands; $i++) {
                        $b = isset($incomingBets[$i]) ? (int) $incomingBets[$i] : ($bets[0] ?? $defaultBet);
                        $b = max(1, $b);
                        $roundBets[$i] = $b;
                        $sum += $b;
                    }

                    if ($profile['bank'] < $sum) {
                        $errors[] = 'Inte tillräckligt med pengar för så många händer.';
                    } else {
                        $profile['bank'] -= $sum;
                        $session->set('blackjack_profile', $profile);
                        $session->set('blackjack_bets', $roundBets);
                        $session->set('blackjack_settled', false);
                        $game = new Game($hands);
                        $session->set('blackjack', $game);
                    }
                } elseif ($game instanceof Game) {
                    switch ($action) {
                        case 'hit':
                            $game->hit();
                            break;
                        case 'stand':
                            $game->stand();
                            break;
                        case 'split':
                            $pre = $game->toArray();
                            $idx = (int) $pre['current'];
                            $canSplit = (bool) ($pre['players'][$idx]['canSplit'] ?? false);
                            if (!$canSplit) {
                                // Can't split this hand
                            } else {
                                $need = (int) ($bets[$idx] ?? 1);
                                if (($profile['bank'] ?? 0) < $need) {
                                    $errors[] = 'Du har inte råd att splitta just nu.';
                                } else {
                                    $profile['bank'] -= $need;
                                    $session->set('blackjack_profile', $profile);
                                    array_splice($bets, $idx + 1, 0, [$need]);
                                    $session->set('blackjack_bets', $bets);
                                    $game->split();
                                }
                            }
                            break;
                        case 'new':
                            $hands = max(1, min(3, (int) ($session->get('blackjack_hands') ?? 1)));
                            $roundBets = [];
                            $sum = 0;
                            for ($i = 0; $i < $hands; $i++) {
                                $b = (int) ($bets[$i] ?? 1);
                                $b = max(1, $b);
                                $roundBets[$i] = $b;
                                $sum += $b;
                            }
                            if ($profile['bank'] < $sum) {
                                $errors[] = 'För lite pengar kvar. Ändra antal händer eller insatser.';
                            } else {
                                $profile['bank'] -= $sum;
                                $session->set('blackjack_profile', $profile);
                                $session->set('blackjack_bets', $roundBets);
                                $session->set('blackjack_settled', false);
                                $game = new Game($hands);
                            }
                            break;
                    }
                    $session->set('blackjack', $game);
                }
            }
        }

        // Check if round is finished and pay out
        $state = $game instanceof Game ? $game->toArray() : null;

        if ($game instanceof Game && $state && ($state['finished'] ?? false)) {
            $settled = (bool) ($session->get('blackjack_settled') ?? false);
            if (!$settled) {
                $results = $state['results'] ?? [];
                $bets    = $session->get('blackjack_bets') ?? [];

                $roundBetSum = 0;
                foreach ($bets as $b) { $roundBetSum += (int)$b; }

                $delta = 0;
                foreach ($results as $i => $r) {
                    $bet = (int) ($bets[$i] ?? 0);
                    if ($bet <= 0) { continue; }

                    $hand = $state['players'][$i] ?? [];
                    $isBj = !empty($hand['bj']) && isset($hand['cards']) && is_array($hand['cards']) && count($hand['cards']) === 2;

                    if (!empty($r['win'])) {
                        // Blackjack pays 3:2
                        $returned = $isBj ? (int) floor($bet * 2.5) : ($bet * 2);
                    } elseif (!empty($r['push'])) {
                        $returned = $bet;
                    } else {
                        $returned = 0;
                    }
                    $delta += $returned;
                }

                $profile = $session->get('blackjack_profile') ?? ['name' => '', 'bank' => 0];
                if ($delta > 0) {
                    $profile['bank'] = (int)$profile['bank'] + $delta;
                    $session->set('blackjack_profile', $profile);
                }
                $session->set('blackjack_settled', true);

                $lastNet = $delta - $roundBetSum;
                $session->set('blackjack_last_net', $lastNet);

                // Track stats
                $stats = $session->get('blackjack_stats') ?? [
                    'rounds' => 0, 'wins' => 0, 'losses' => 0, 'pushes' => 0,
                    'bj' => 0, 'busts' => 0, 'splits' => 0,
                    'wagered' => 0, 'returned' => 0, 'net' => 0
                ];

                $wins = 0; $losses = 0; $pushes = 0; $bjCount = 0; $bustCount = 0;
                foreach ($state['players'] ?? [] as $h) {
                    if (!empty($h['bj'])) { $bjCount++; }
                    if (!empty($h['bust'])) { $bustCount++; }
                }
                foreach ($results as $r) {
                    if (!empty($r['win'])) { $wins++; }
                    elseif (!empty($r['push'])) { $pushes++; }
                    else { $losses++; }
                }

                $stats['rounds']  = (int)$stats['rounds']  + 1;
                $stats['wins']    = (int)$stats['wins']    + $wins;
                $stats['losses']  = (int)$stats['losses']  + $losses;
                $stats['pushes']  = (int)$stats['pushes']  + $pushes;
                $stats['bj']      = (int)$stats['bj']      + $bjCount;
                $stats['busts']   = (int)$stats['busts']   + $bustCount;
                $stats['wagered'] = (int)$stats['wagered'] + (int)$roundBetSum;
                $stats['returned']= (int)$stats['returned']+ (int)$delta;
                $stats['net']     = (int)$stats['net']     + (int)$lastNet;

                $session->set('blackjack_stats', $stats);
            }
        }

        // Pass data to view
        ob_start();
        $stateVar = $state;
        $profileVar = $profile;
        $betsVar = $session->get('blackjack_bets') ?? [];
        $errorsVar = $errors;
        $statsVar = $session->get('blackjack_stats') ?? [
            'rounds' => 0, 'wins' => 0, 'losses' => 0, 'pushes' => 0,
            'bj' => 0, 'busts' => 0,
            'wagered' => 0, 'returned' => 0, 'net' => 0
        ];
        $lastNetVar = $session->get('blackjack_last_net');
        $bankEmptyVar = (bool) ((int)($profile['bank'] ?? 0) <= 0);
        $baseVar = $request->getBasePath();
        $route = ($baseVar ?? '') . '/blackjack';
        include __DIR__ . '/../../proj/blackjack/index.php';
        $html = ob_get_clean();

        return new Response($html);
    }
}