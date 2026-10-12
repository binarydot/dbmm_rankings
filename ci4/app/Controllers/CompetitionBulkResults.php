<?php

namespace App\Controllers;

use App\Models\CompetitionBulkResultsModel;
use App\Models\CompetitionMatchesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionBulkResults extends BaseController
{
    public function add(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId);
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        return view('competitions/bulk-results-upload', [
            'title' => 'Add All Competition Results',
            'menuItem' => 'competitions',
            'competition' => $competition,
        ]);
    }

    public function preview(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId);
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        if ($competition['type'] !== 'Singles') {
            return redirect()->to(site_url('competition/results/' . $competitionId))
                ->with('error', 'Bulk CSV results are supported for singles competitions only.');
        }

        $file = $this->request->getFile('csvfile');
        if ($file === null || ! $file->isValid() || $file->hasMoved() || $file->getSize() > 102400
            || strtolower($file->getClientExtension()) !== 'csv') {
            return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                ->with('error', 'Select a valid CSV file no larger than 100 KB.');
        }

        $parsed = $this->parseCsv($file->getTempName());
        if (isset($parsed['error'])) {
            return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                ->with('error', $parsed['error']);
        }

        $rows = (new CompetitionBulkResultsModel())->mapRows($parsed['rows']);
        $matchBuild = $this->buildMatches($rows, $parsed['rounds']);
        if (isset($matchBuild['error'])) {
            return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                ->with('error', $matchBuild['error']);
        }

        $token = bin2hex(random_bytes(24));
        $previews = session()->get('bulkResultPreviews') ?? [];
        $previews = is_array($previews) ? $previews : [];
        $previews[$token] = [
            'competitionId' => (int) $competitionId,
            'rows' => $parsed['rows'],
            'matches' => $matchBuild['matches'],
            'rounds' => $parsed['rounds'],
        ];
        session()->set('bulkResultPreviews', $previews);

        $options = (new CompetitionBulkResultsModel())->getSelectionOptions();
        return view('competitions/bulk-results-preview', [
            'title' => 'Validate Competition Results',
            'menuItem' => 'competitions',
            'competition' => $competition,
            'rows' => $rows,
            'matches' => $matchBuild['matches'],
            'token' => $token,
            'competitors' => $options['competitors'],
            'armies' => $options['armies'],
            'selectedPlayers' => array_map(static fn (array $row): int => count($row['playerOptions']) === 1 ? (int) $row['playerOptions'][0]['competitor_id'] : 0, $rows),
            'selectedArmies' => array_map(static fn (array $row): int => count($row['armyOptions']) === 1 ? (int) $row['armyOptions'][0]['army_id'] : 0, $rows),
        ]);
    }

    public function insert(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId);
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }
        if ($competition['type'] !== 'Singles') {
            return redirect()->to(site_url('competition/results/' . $competitionId));
        }

        $post = (array) $this->request->getPost();
        $token = $post['token'] ?? '';
        $previews = session()->get('bulkResultPreviews') ?? [];
        if (! is_string($token) || ! preg_match('/^[a-f0-9]{48}$/', $token)
            || ! is_array($previews) || ! isset($previews[$token])
            || $previews[$token]['competitionId'] !== (int) $competitionId) {
            return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                ->with('error', 'The validated CSV preview has expired. Upload the file again.');
        }

        $preview = $previews[$token];
        $rows = $preview['rows'];
        $options = (new CompetitionBulkResultsModel())->getSelectionOptions();
        $resolvedRows = [];
        foreach ($rows as $index => $row) {
            $competitorId = $this->postedPositiveId($post, 'p' . $index);
            $armyId = $this->postedPositiveId($post, 'a' . $index);
            if ($competitorId === null || $armyId === null
                || ! $this->optionContains($options['competitors'], 'competitor_id', $competitorId)
                || ! $this->optionContains($options['armies'], 'army_id', $armyId)) {
                return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                    ->with('error', 'The preview contains unresolved or invalid player/army selections. Upload the file again.');
            }

            $resolvedRows[] = [
                'competitorId' => $competitorId,
                'armyId' => $armyId,
                'position' => $row['position'],
                'score' => $row['score'],
            ];
        }

        $fileToCompetitor = [];
        $selectedCompetitorIds = [];
        foreach ($rows as $index => $row) {
            $competitorId = $resolvedRows[$index]['competitorId'];
            if (isset($fileToCompetitor[$row['fileId']]) || isset($selectedCompetitorIds[$competitorId])) {
                return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                    ->with('error', 'Each CSV player ID and selected competitor must be unique. Correct the selections and upload the file again.');
            }
            $fileToCompetitor[$row['fileId']] = $competitorId;
            $selectedCompetitorIds[$competitorId] = true;
        }

        $matches = [];
        foreach ($preview['matches'] as $match) {
            $winnerId = $fileToCompetitor[$match['winnerFileId']] ?? null;
            $loserId = $match['loserFileId'] === null ? null : ($fileToCompetitor[$match['loserFileId']] ?? null);
            if ($winnerId === null || ($match['loserFileId'] !== null && $loserId === null)
                || ($loserId !== null && $winnerId === $loserId)) {
                return redirect()->to(site_url('competition/addAllResults/' . $competitionId))
                    ->with('error', 'The validated match pairings could not be resolved. Upload the file again.');
            }
            $matches[] = [
                'round' => $match['round'],
                'winnerId' => $winnerId,
                'loserId' => $loserId,
                'winnerScore' => $match['winnerScore'],
                'loserScore' => $match['loserScore'],
            ];
        }

        if (! (new CompetitionBulkResultsModel())->replaceResults(
            (int) $competitionId,
            $competition['competition_date'],
            $resolvedRows,
            $matches
        )) {
            throw new \RuntimeException('Unable to replace competition results from the validated CSV.');
        }

        unset($previews[$token]);
        session()->set('bulkResultPreviews', $previews);
        return redirect()->to(site_url('competition/results/' . $competitionId));
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Unable to read uploaded competition results CSV.');
        }

        $rows = [];
        $rounds = null;
        $line = 0;
        while (($columns = fgetcsv($handle, 10000, ',')) !== false) {
            if (count($columns) === 1 && trim((string) $columns[0]) === '') {
                continue;
            }
            $line++;
            if (count($columns) < 7) {
                fclose($handle);
                return ['error' => 'CSV line ' . $line . ' has too few columns.'];
            }
            if ($rounds === null) {
                $roundColumnCount = count($columns) - 5;
                if ($roundColumnCount < 2 || $roundColumnCount % 2 !== 0) {
                    fclose($handle);
                    return ['error' => 'The CSV must have a matching opponent and score column for each round.'];
                }
                $rounds = (int) ($roundColumnCount / 2);
            }
            if (count($columns) !== 5 + 2 * $rounds) {
                fclose($handle);
                return ['error' => 'CSV line ' . $line . ' has a different number of columns than the first line.'];
            }
            if (! ctype_digit(trim((string) $columns[0])) || (int) $columns[0] < 1
                || trim((string) $columns[1]) === '' || trim((string) $columns[2]) === '') {
                fclose($handle);
                return ['error' => 'CSV line ' . $line . ' is missing a valid position, file player ID, or player name.'];
            }

            $opponents = array_slice($columns, 3, $rounds);
            $matchScores = array_slice($columns, 3 + $rounds, $rounds);
            $overallScore = trim((string) $columns[3 + 2 * $rounds]);
            if (! is_numeric($overallScore) || ! is_finite((float) $overallScore) || (float) $overallScore < 0
                || ! $this->allNumericScores($matchScores)) {
                fclose($handle);
                return ['error' => 'CSV line ' . $line . ' contains an invalid result or match score.'];
            }
            foreach ($opponents as $opponent) {
                $opponent = strtoupper(trim((string) $opponent));
                if ($opponent === '') {
                    fclose($handle);
                    return ['error' => 'CSV line ' . $line . ' contains an empty opponent value.'];
                }
            }

            $rows[] = [
                'position' => (int) $columns[0],
                'fileId' => trim((string) $columns[1]),
                'name' => trim((string) $columns[2]),
                'opponents' => array_map(static fn ($value): string => trim((string) $value), $opponents),
                'matchScores' => array_map(static fn ($value): float => (float) $value, $matchScores),
                'score' => (float) $overallScore,
                'army' => trim((string) $columns[count($columns) - 1]),
            ];
            if (count($rows) > 500) {
                fclose($handle);
                return ['error' => 'The CSV exceeds the 500 competitor limit for one bulk upload.'];
            }
        }
        fclose($handle);

        if ($rows === [] || $rounds === null) {
            return ['error' => 'The uploaded CSV contains no result rows.'];
        }

        $fileIds = array_column($rows, 'fileId');
        if (count(array_unique($fileIds)) !== count($fileIds)) {
            return ['error' => 'The CSV contains duplicate player IDs.'];
        }

        return ['rows' => $rows, 'rounds' => $rounds];
    }

    private function buildMatches(array $rows, int $rounds): array
    {
        $byFileId = [];
        foreach ($rows as $row) {
            $byFileId[$row['fileId']] = $row;
        }
        $matches = [];
        for ($round = 1; $round <= $rounds; $round++) {
            $done = [];
            foreach ($rows as $row) {
                $fileId = $row['fileId'];
                if (isset($done[$fileId])) {
                    continue;
                }

                $opponentId = trim((string) $row['opponents'][$round - 1]);
                if (strtoupper($opponentId) === 'X') {
                    $done[$fileId] = true;
                    continue;
                }
                if (strtoupper($opponentId) === 'B') {
                    $matches[] = [
                        'round' => $round, 'winnerFileId' => $fileId, 'loserFileId' => null,
                        'winnerScore' => $row['matchScores'][$round - 1], 'loserScore' => 0.0,
                    ];
                    $done[$fileId] = true;
                    continue;
                }
                if (! isset($byFileId[$opponentId]) || $opponentId === $fileId
                    || isset($done[$opponentId])
                    || trim((string) $byFileId[$opponentId]['opponents'][$round - 1]) !== $fileId) {
                    return ['error' => 'Round ' . $round . ' has a missing, repeated, or non-reciprocal opponent for ' . $row['name'] . '.'];
                }

                $opponent = $byFileId[$opponentId];
                $playerScore = $row['matchScores'][$round - 1];
                $opponentScore = $opponent['matchScores'][$round - 1];
                if ($playerScore >= $opponentScore) {
                    $winner = $row;
                    $loser = $opponent;
                } else {
                    $winner = $opponent;
                    $loser = $row;
                }
                $matches[] = [
                    'round' => $round,
                    'winnerFileId' => $winner['fileId'],
                    'loserFileId' => $loser['fileId'],
                    'winnerScore' => max($playerScore, $opponentScore),
                    'loserScore' => min($playerScore, $opponentScore),
                ];
                $done[$fileId] = true;
                $done[$opponentId] = true;
            }
        }

        return ['matches' => $matches];
    }

    private function allNumericScores(array $scores): bool
    {
        foreach ($scores as $score) {
            if (! is_numeric(trim((string) $score)) || ! is_finite((float) $score) || (float) $score < 0 || (float) $score > 25) {
                return false;
            }
        }

        return true;
    }

    private function optionContains(array $options, string $field, int $id): bool
    {
        foreach ($options as $option) {
            if ((int) $option[$field] === $id) {
                return true;
            }
        }

        return false;
    }

    private function postedPositiveId(array $post, string $key): ?int
    {
        $value = $post[$key] ?? null;
        return is_scalar($value) && ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
    }

    private function authorizedCompetition(string $competitionId): array|RedirectResponse
    {
        if (! ctype_digit($competitionId) || (int) $competitionId < 1) {
            throw PageNotFoundException::forPageNotFound();
        }
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $competition = (new CompetitionMatchesModel())->getCompetition((int) $competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }
        $permissions = session()->get('permissions') ?? [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (($permissions['Add Result'] ?? 0) != 1 || ! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        return $competition;
    }
}
