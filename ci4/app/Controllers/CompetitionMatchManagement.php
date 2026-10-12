<?php

namespace App\Controllers;

use App\Models\CompetitionMatchManagementModel;
use App\Models\CompetitionMatchesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CompetitionMatchManagement extends BaseController
{
    public function add(string $competitionId): string|RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        return $this->matchForm((int) $competitionId, $competition, null);
    }

    public function insert(string $competitionId): RedirectResponse
    {
        $competition = $this->authorizedCompetition($competitionId, 'Add Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $data = $this->postedMatch();
        $validation = service('validation');
        if (! $validation->run($data, $this->rules())) {
            return redirect()->to(site_url('competition/addMatch/' . $competitionId))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $matchData = $this->validatedMatchData($data, (int) $competitionId);
        if ($matchData === null) {
            return redirect()->to(site_url('competition/addMatch/' . $competitionId))
                ->withInput()
                ->with('error', 'Select valid, distinct competitors and active armies for both sides.');
        }

        if ((new CompetitionMatchManagementModel())->saveMatch(
            (int) $competitionId,
            null,
            $matchData['match'],
            $matchData['participants']
        ) === false) {
            throw new \RuntimeException('Unable to add competition match.');
        }

        return redirect()->to(site_url('competition/matches/' . $competitionId));
    }

    public function edit(string $competitionId, string $matchId): string|RedirectResponse
    {
        $this->validateId($matchId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $model = new CompetitionMatchManagementModel();
        $match = $model->getMatch((int) $competitionId, (int) $matchId);
        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Competition match does not exist.');
        }

        return $this->matchForm((int) $competitionId, $competition, $match);
    }

    public function update(string $competitionId, string $matchId): RedirectResponse
    {
        $this->validateId($matchId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $model = new CompetitionMatchManagementModel();
        if ($model->getMatch((int) $competitionId, (int) $matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Competition match does not exist.');
        }

        $data = $this->postedMatch();
        $validation = service('validation');
        if (! $validation->run($data, $this->rules())) {
            return redirect()->to(site_url('competition/editMatch/' . $competitionId . '/' . $matchId))
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $matchData = $this->validatedMatchData($data, (int) $competitionId);
        if ($matchData === null) {
            return redirect()->to(site_url('competition/editMatch/' . $competitionId . '/' . $matchId))
                ->withInput()
                ->with('error', 'Select valid, distinct competitors and active armies for both sides.');
        }

        if ($model->saveMatch(
            (int) $competitionId,
            (int) $matchId,
            $matchData['match'],
            $matchData['participants']
        ) === false) {
            throw new \RuntimeException('Unable to update competition match.');
        }

        return redirect()->to(site_url('competition/matches/' . $competitionId));
    }

    public function confirmDelete(string $competitionId, string $matchId): string|RedirectResponse
    {
        $this->validateId($matchId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $match = (new CompetitionMatchManagementModel())->getMatch((int) $competitionId, (int) $matchId);
        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Competition match does not exist.');
        }

        return view('competitions/match-delete', [
            'title' => 'Delete Match',
            'menuItem' => 'competitions',
            'competition' => $competition,
            'match' => $match,
        ]);
    }

    public function delete(string $competitionId, string $matchId): RedirectResponse
    {
        $this->validateId($matchId);
        $competition = $this->authorizedCompetition($competitionId, 'Update Matches');
        if ($competition instanceof RedirectResponse) {
            return $competition;
        }

        $model = new CompetitionMatchManagementModel();
        if ($model->getMatch((int) $competitionId, (int) $matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Competition match does not exist.');
        }
        if (! $model->deleteMatch((int) $competitionId, (int) $matchId)) {
            throw new \RuntimeException('Unable to delete competition match.');
        }

        return redirect()->to(site_url('competition/matches/' . $competitionId));
    }

    private function matchForm(int $competitionId, array $competition, ?array $match): string
    {
        $model = new CompetitionMatchManagementModel();
        return view('competitions/match-form', [
            'title' => $match === null ? 'Add Match' : 'Edit Match',
            'menuItem' => 'competitions',
            'competition' => $competition,
            'match' => $match,
            'competitors' => $model->getCompetitors($competitionId),
            'armies' => $model->getArmies(),
        ]);
    }

    private function authorizedCompetition(string $competitionId, string $permission): array|RedirectResponse
    {
        $this->validateId($competitionId);
        if (! session()->get('loggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $competition = (new CompetitionMatchesModel())->getCompetition((int) $competitionId);
        if ($competition === null) {
            throw PageNotFoundException::forPageNotFound('Competition does not exist.');
        }

        $permissions = session()->get('permissions') ?? [];
        $permissions = is_array($permissions) ? $permissions : [];
        $seriesPermissions = session()->get('seriesPermissions') ?? [];
        $seriesPermissions = is_array($seriesPermissions) ? $seriesPermissions : [];
        if (($permissions[$permission] ?? 0) != 1 || ! isset($seriesPermissions[$competition['competition_series_id']])) {
            return redirect()->to(site_url('competition'));
        }

        return $competition;
    }

    private function validateId(string $id): void
    {
        if (! ctype_digit($id) || (int) $id < 1) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function postedMatch(): array
    {
        $post = (array) service('request')->getPost();
        $scalar = static fn ($value, string $default = '') => is_scalar($value) ? (string) $value : $default;
        return [
            'date' => $scalar($post['matchdate'] ?? null),
            'glicko_eligible' => $scalar($post['glicko_eligible'] ?? null),
            'round' => $scalar($post['round'] ?? null),
            'winner_ids' => [$scalar($post['winnerid0'] ?? null), $scalar($post['winnerid1'] ?? '-1', '-1')],
            'loser_ids' => [$scalar($post['loserid0'] ?? null), $scalar($post['loserid1'] ?? '-1', '-1')],
            'winner_army' => $scalar($post['winnerarmy'] ?? '-1', '-1'),
            'loser_army' => $scalar($post['loserarmy'] ?? '-1', '-1'),
            'winner_score' => $scalar($post['winnerscore'] ?? null),
            'loser_score' => $scalar($post['loserscore'] ?? null),
        ];
    }

    private function rules(): array
    {
        return [
            'date' => 'required|valid_date[Y-m-d]',
            'glicko_eligible' => 'required|in_list[0,1]',
            'round' => 'required|is_natural_no_zero',
            'winner_score' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[25]',
            'loser_score' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[25]',
            'winner_ids' => 'required',
            'loser_ids' => 'required',
        ];
    }

    private function validatedMatchData(array $data, int $competitionId): ?array
    {
        if ((float) $data['winner_score'] < (float) $data['loser_score']) {
            return null;
        }

        $model = new CompetitionMatchManagementModel();
        $validCompetitorIds = array_fill_keys(
            array_map('intval', array_column($model->getCompetitors($competitionId), 'competitor_id')),
            true
        );
        $validArmyIds = array_fill_keys(
            array_map('intval', array_column($model->getArmies(), 'army_id')),
            true
        );

        $winnerIds = $this->sideIds($data['winner_ids'], $validCompetitorIds);
        $loserIds = $this->sideIds($data['loser_ids'], $validCompetitorIds);
        $winnerArmy = $this->armyId($data['winner_army'], $validArmyIds);
        $loserArmy = $this->armyId($data['loser_army'], $validArmyIds);
        if ($winnerIds === null || $loserIds === null || $winnerArmy === false || $loserArmy === false
            || ($winnerIds === [] && $loserIds === [])
            || array_intersect($winnerIds, $loserIds) !== []) {
            return null;
        }

        $participants = [];
        foreach ($winnerIds as $competitorId) {
            $participants[] = ['competitor_id' => $competitorId, 'side' => 'W', 'army_id' => $winnerArmy];
        }
        foreach ($loserIds as $competitorId) {
            $participants[] = ['competitor_id' => $competitorId, 'side' => 'L', 'army_id' => $loserArmy];
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $data['date']);
        if ($date === false) {
            return null;
        }
        $match = [
            'round' => (int) $data['round'],
            'winner_score' => $data['winner_score'],
            'loser_score' => $data['loser_score'],
            'glicko_eligible' => (int) $data['glicko_eligible'],
            'match_date' => $date->format('Y-m-d'),
            'match_seqno' => $date->format('Ymd') . (int) $data['round'],
        ];

        return ['match' => $match, 'participants' => $participants];
    }

    private function sideIds(array $ids, array $validIds): ?array
    {
        $selected = [];
        foreach ($ids as $id) {
            if ($id === '-1' || $id === '') {
                continue;
            }
            if (! ctype_digit($id) || (int) $id < 1 || ! isset($validIds[(int) $id])) {
                return null;
            }
            $selected[] = (int) $id;
        }

        if (count($selected) > 2 || count(array_unique($selected)) !== count($selected)) {
            return null;
        }

        return $selected;
    }

    private function armyId(string $value, array $validIds): int|null|false
    {
        if ($value === '-1' || $value === '') {
            return null;
        }
        if (! ctype_digit($value) || (int) $value < 1 || ! isset($validIds[(int) $value])) {
            return false;
        }
        return (int) $value;
    }
}
