<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;

class LoginRedirectController extends Controller
{
    public function admin(){
        $now = now();

        /* ----- article numbers (one query) ----- */
        $a = Article::selectRaw(
            'COUNT(*) as total,
            SUM(is_published = 0) as draft,
            SUM(is_published = 1 AND published_at <= ?) as published,
            SUM(is_published = 1 AND published_at > ?) as scheduled',
            [$now, $now]
        )->first();

        $articles = [
            'total'     => (int) ($a->total ?? 0),
            'published' => (int) ($a->published ?? 0),
            'draft'     => (int) ($a->draft ?? 0),
            'scheduled' => (int) ($a->scheduled ?? 0),
        ];

        /* ----- user numbers (one query) ----- */
        $u = User::selectRaw(
            "COUNT(*) as total,
            SUM(role = 'admin') as admin,
            SUM(status = 1) as active,
            SUM(status = 0) as suspended"
        )->first();

        $users = [
            'total'     => (int) ($u->total ?? 0),
            'admin'     => (int) ($u->admin ?? 0),
            'active'    => (int) ($u->active ?? 0),
            'suspended' => (int) ($u->suspended ?? 0),
        ];

        /* ----- published per month, last 6 months ----- */
        $start   = now()->startOfMonth()->subMonths(5);
        $byMonth = Article::published()
            ->where('published_at', '>=', $start)
            ->pluck('published_at')
            ->groupBy(fn ($d) => $d->format('Y-m'));

        $months = collect(range(0, 5))->map(function ($i) use ($start, $byMonth) {
            $m = $start->copy()->addMonths($i);
            return [
                'label' => $m->format('M'),
                'count' => $byMonth->get($m->format('Y-m'), collect())->count(),
            ];
        });
        $maxMonth = max(1, $months->max('count'));

        /* ----- GEO readiness (share of published articles) ----- */
        $pub  = $articles['published'];
        $pct  = fn ($n) => $pub > 0 ? (int) round($n / $pub * 100) : 0;
        $live = fn () => Article::published();

        $geo = [
            ['label' => 'Key takeaways',     'icon' => 'bi-lightbulb',        'value' => $pct($live()->whereNotNull('key_takeaways')->count())],
            ['label' => 'FAQ',               'icon' => 'bi-question-circle',  'value' => $pct($live()->whereNotNull('faq')->count())],
            ['label' => 'Sources',           'icon' => 'bi-journal-bookmark', 'value' => $pct($live()->whereNotNull('source_references')->count())],
            ['label' => 'Reviewed (6 mo)',   'icon' => 'bi-patch-check',      'value' => $pct($live()->where('last_reviewed_at', '>=', now()->subMonths(6))->count())],
        ];

        /* ----- needs attention ----- */
        $attention = [
            'staleDrafts' => Article::where('is_published', false)->where('updated_at', '<', now()->subDays(14))->count(),
            'unreviewed'  => Article::published()->where(fn ($q) => $q->whereNull('last_reviewed_at')
                                ->orWhere('last_reviewed_at', '<', now()->subMonths(6)))->count(),
        ];

        /* ----- lists ----- */
        $topCategories = Category::withCount(['articles' => fn ($q) => $q->published()])
            ->orderByDesc('articles_count')->orderBy('name')->take(5)->get();
        $maxCat = max(1, $topCategories->max('articles_count'));

        $recentArticles = Article::with(['category:id,name', 'user:id,name'])
            ->latest('updated_at')->take(6)->get();

        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'articles', 'users', 'months', 'maxMonth', 'geo', 'attention',
            'topCategories', 'maxCat', 'recentArticles', 'recentUsers'
        ));
    }

    public function user(){
        return view('dashboard.user');
    }
}
