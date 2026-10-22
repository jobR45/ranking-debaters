<template>
    <div
        v-if="debater"
        class="min-h-screen bg-gray-950 text-gray-100 font-['Cairo'] pb-16"
        dir="rtl"
    >
        <!-- Top Header Navigation -->
        <header
            class="bg-gray-900 border-b border-gray-800 py-4 px-6 sticky top-0 z-10 flex justify-between items-center"
        >
            <router-link
                to="/"
                class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-amber-400 px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm"
            >
                ➔ العودة إلى الترتيب العام
            </router-link>

            <!-- Login / User Profile Component -->
            <AuthNavbar />
        </header>

        <div class="max-w-6xl mx-auto px-6 mt-8">
            <!-- Debater Overview Header Card -->
            <div
                class="bg-gray-900 border border-gray-800 rounded-2xl p-8 mb-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6"
            >
                <div>
                    <h1
                        class="text-3xl md:text-4xl font-extrabold text-white mb-2"
                    >
                        {{ debater.name_ar }}
                    </h1>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-2xl">
                        {{ debater.bio || "مناظر مسجل في القائمة الرسمية." }}
                    </p>
                </div>
                <div
                    class="flex gap-4 bg-gray-800/80 p-4 rounded-xl border border-gray-700/60 shadow-inner"
                >
                    <div class="text-center px-4 border-l border-gray-700">
                        <span class="block text-xs text-gray-400"
                            >النتيجة الرسمية</span
                        >
                        <span class="text-2xl font-black text-amber-400"
                            >{{
                                debater.official_score?.total_score || 0
                            }}/100</span
                        >
                    </div>
                    <div class="text-center px-4">
                        <span class="block text-xs text-gray-400"
                            >تقييم الجمهور</span
                        >
                        <span class="text-2xl font-black text-blue-400">{{
                            debater.audience_score !== null &&
                                debater.audience_score !== undefined
                                ? debater.audience_score + "%"
                                : "لا يوجد"
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Sidebar: Score Breakdown & Rating Form -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Official Criteria Scores -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-lg"
                    >
                        <h3
                            class="text-lg font-bold text-white mb-4 flex items-center gap-2"
                        >
                            <span>📊</span> معايير التقييم الرسمية
                        </h3>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-800 text-sm"
                            >
                                <span class="text-gray-400">آداب النقاش</span>
                                <span class="font-bold text-amber-400"
                                    >{{
                                        debater.official_score?.etiquette || 0
                                    }}
                                    / 25</span
                                >
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-800 text-sm"
                            >
                                <span class="text-gray-400"
                                    >المعرفة والتخصص</span
                                >
                                <span class="font-bold text-amber-400"
                                    >{{
                                        debater.official_score?.knowledge || 0
                                    }}
                                    / 25</span
                                >
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-800 text-sm"
                            >
                                <span class="text-gray-400">المنطق والحجة</span>
                                <span class="font-bold text-amber-400"
                                    >{{ debater.official_score?.logic || 0 }} /
                                    25</span
                                >
                            </div>
                            <div
                                class="flex justify-between items-center py-2 text-sm"
                            >
                                <span class="text-gray-400"
                                    >الفوز بالمناظرة</span
                                >
                                <span class="font-bold text-amber-400"
                                    >{{
                                        debater.official_score?.victory || 0
                                    }}
                                    / 25</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Audience Review Actions -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-lg"
                    >
                        <h3
                            class="text-lg font-bold text-white mb-1 flex items-center gap-2"
                        >
                            <span>⭐</span> تقييم الجمهور
                        </h3>
                        <p class="text-xs text-gray-400 mb-4">
                            شارك رأيك وتقييمك للمناظر
                        </p>
                        <Button
                            :label="ownReview ? 'تعديل تقييمك' : 'إضافة تقييم'"
                            severity="warn"
                            class="w-full"
                            @click="ownReview ? openEditReview(ownReview) : openCreateReview()"
                        />
                    </div>
                </div>

                <!-- Right Main Column: Official AI Review + Audience Feed -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- SECTION 1: OFFICIAL DEBATE REVIEW (MEMBER / AI ANALYTICS) -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl"
                    >
                        <div
                            class="flex items-center justify-between border-b border-gray-800 pb-4 mb-4"
                        >
                            <h2
                                class="text-xl font-bold text-white flex items-center gap-2"
                            >
                                <span>📑</span> المراجعة التحليلية الرسمية
                            </h2>
                            <span
                                class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full font-bold"
                            >
                                تقييم موثق
                            </span>
                        </div>

                        <div
                            v-if="
                                debater.debater_review
                            "
                            class="space-y-6"
                        >
                            <div
                                class="bg-gray-800/40 border border-gray-800 rounded-xl p-5"
                            >
                                <h3
                                    class="text-lg font-bold text-amber-400 mb-3"
                                >
                                    {{ debater.debater_review.title }}
                                </h3>

                                <!-- Formatted Markdown Content (Bold Topics + Ordered List) -->
                                <WrittenReview
                                    :content="debater.debater_review.written_review"
                                />

                                <div
                                    v-if="debater.debater_review.video_url"
                                    class="mt-4 pt-3 border-t border-gray-800/80 flex items-center justify-between"
                                >
                                    <a
                                        :href="debater.debater_review.video_url"
                                        target="_blank"
                                        class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1"
                                    >
                                        🎥 مشاهدة فيديو المناظرة Complete
                                        Recording
                                    </a>
                                    <span class="text-xs text-gray-500"
                                        >❤️
                                        {{ debater.debater_review.likes_count || 0 }}
                                        إعجاب</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-center py-8 text-gray-500 text-sm"
                        >
                            لا توجد مراجعة رسمية مكتوبة مخصصة لهذا المناظر
                            حالياً.
                        </div>
                    </div>

                    <!-- SECTION 2: AUDIENCE REVIEWS & COMMUNITY FEED -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl"
                    >
                        <div
                            class="flex items-center justify-between border-b border-gray-800 pb-4 mb-4"
                        >
                            <h2
                                class="text-xl font-bold text-white flex items-center gap-2"
                            >
                                <span>💬</span> تقييمات وتعليقات المتابعين
                            </h2>
                            <span class="text-xs text-gray-400 font-bold">
                                ({{ totalReviews }} تقييم)
                            </span>
                        </div>

                        <div v-if="reviewsLoading" class="py-8 text-center text-gray-400">
                            جارٍ تحميل التقييمات...
                        </div>

                        <div
                            v-else-if="audienceReviews.length > 0"
                            class="space-y-4"
                        >
                            <div
                                v-for="aud in audienceReviews"
                                :key="aud.id"
                                class="bg-gray-800/50 border border-gray-700/50 p-4 rounded-xl"
                            >
                                <div
                                    class="flex flex-wrap justify-between items-center gap-3 mb-2"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 bg-amber-500/20 text-amber-400 font-bold rounded-full flex items-center justify-center text-xs"
                                        >
                                            {{
                                                aud.reviewer_name
                                                    ? aud.reviewer_name.charAt(
                                                          0,
                                                      )
                                                    : "ز"
                                            }}
                                        </span>
                                        <span
                                            class="font-bold text-sm text-white"
                                            >{{
                                                aud.reviewer_name || "زائر"
                                            }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-xs font-bold text-blue-400 bg-blue-950/80 border border-blue-800/80 px-2.5 py-1 rounded-lg"
                                    >
                                        النتيجة: {{ aud.total_score }}/100
                                    </span>
                                    <div v-if="aud.is_owner" class="flex gap-2">
                                        <Button
                                            label="تعديل"
                                            severity="secondary"
                                            size="small"
                                            text
                                            @click="openEditReview(aud)"
                                        />
                                        <Button
                                            label="حذف"
                                            severity="danger"
                                            size="small"
                                            text
                                            @click="confirmDeleteReview(aud)"
                                        />
                                    </div>
                                </div>

                                <p
                                    v-if="aud.comment"
                                    :class="[
                                        'text-gray-300 text-xs leading-relaxed bg-gray-900/40 p-3 rounded-lg border border-gray-800/60 mt-2 whitespace-pre-wrap',
                                        { 'line-clamp-3': aud.comment.length > 220 && !isCommentExpanded(aud.id) },
                                    ]"
                                >
                                    {{ aud.comment }}
                                </p>
                                <Button
                                    v-if="aud.comment && aud.comment.length > 220"
                                    :label="isCommentExpanded(aud.id) ? 'عرض أقل' : 'عرض المزيد'"
                                    severity="secondary"
                                    size="small"
                                    text
                                    class="mt-1"
                                    @click="toggleComment(aud.id)"
                                />

                                <div
                                    class="flex justify-between items-center text-[10px] text-gray-500 mt-3 pt-2 border-t border-gray-800/40"
                                >
                                    <span
                                        >آداب: {{ aud.etiquette }} | معرفة:
                                        {{ aud.knowledge }} | منطق:
                                        {{ aud.logic }} | فوز:
                                        {{ aud.victory }}</span
                                    >
                                    <span>{{
                                        formatDate(aud.created_at)
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else-if="!reviewsLoading"
                            class="text-center py-8 text-gray-500 text-sm"
                        >
                            لا توجد تقييمات من المتابعين بعد. شارك بتقييمك الأول!
                        </div>

                        <Paginator
                            v-if="totalReviews > reviewsPerPage"
                            :first="(currentPage - 1) * reviewsPerPage"
                            :rows="reviewsPerPage"
                            :totalRecords="totalReviews"
                            template="PrevPageLink PageLinks NextPageLink"
                            class="mt-5"
                            @page="onReviewsPage"
                        />
                    </div>
                </div>
            </div>
        </div>

        <Dialog
            v-model:visible="reviewDialogVisible"
            modal
            :header="isEditingReview ? 'تعديل تقييم الجمهور' : 'إضافة تقييم الجمهور'"
            :style="{ width: 'min(92vw, 36rem)' }"
            :breakpoints="{ '640px': '95vw' }"
        >
            <form class="space-y-5" dir="rtl" @submit.prevent="saveAudienceReview">
                <p class="text-sm text-gray-400">
                    أدخل درجة من 0 إلى 25 لكل محور.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label
                        v-for="criterion in reviewCriteria"
                        :key="criterion.key"
                        class="flex flex-col gap-2 text-sm text-gray-200"
                    >
                        <span>{{ criterion.label }} (25)</span>
                        <InputNumber
                            v-model="ratingForm[criterion.key]"
                            :min="0"
                            :max="25"
                            :useGrouping="false"
                            required
                            class="w-full"
                            inputClass="w-full"
                        />
                    </label>
                </div>

                <label class="flex flex-col gap-2 text-sm text-gray-200">
                    <span>التعليق/الملاحظة</span>
                    <Textarea
                        v-model="ratingForm.comment"
                        rows="4"
                        maxlength="500"
                        autoResize
                        class="w-full"
                        placeholder="اكتب رأيك باختصار..."
                    />
                    <span class="text-xs text-gray-400">
                        {{ ratingForm.comment.length }}/500
                    </span>
                </label>

                <div class="flex justify-end gap-3">
                    <Button
                        label="إلغاء"
                        severity="secondary"
                        outlined
                        type="button"
                        @click="reviewDialogVisible = false"
                    />
                    <Button
                        :label="isEditingReview ? 'حفظ التعديلات' : 'إرسال التقييم'"
                        severity="warn"
                        type="submit"
                        :loading="savingReview"
                    />
                </div>
            </form>
        </Dialog>
        <ConfirmDialog group="audience-review" />
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { toast } from "vue3-toastify";
import { useConfirm } from "primevue/useconfirm";
import Button from "primevue/button";
import ConfirmDialog from "primevue/confirmdialog";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import Paginator from "primevue/paginator";
import Textarea from "primevue/textarea";
import AuthNavbar from "./AuthNavbar.vue";
import WrittenReview from "./WrittenReview.vue";
import { useAuth } from "../store/auth.js";

const auth = useAuth();
const confirm = useConfirm();
const route = useRoute();
const debater = ref(null);
const audienceReviews = ref([]);
const ownReview = ref(null);
const currentPage = ref(1);
const totalReviews = ref(0);
const reviewsPerPage = 5;
const reviewsLoading = ref(false);
const reviewDialogVisible = ref(false);
const isEditingReview = ref(false);
const editingReviewId = ref(null);
const savingReview = ref(false);
const expandedComments = ref(new Set());

const reviewCriteria = [
    { key: "etiquette", label: "آداب النقاش" },
    { key: "knowledge", label: "المعرفة والتخصص" },
    { key: "logic", label: "المنطق والحجة" },
    { key: "victory", label: "الفوز بالمناظرة" },
];

const ratingForm = ref({
    etiquette: 15,
    knowledge: 15,
    logic: 15,
    victory: 15,
    comment: "",
});

const fetchDebater = async () => {
    try {
        const res = await fetch(`/api/debaters/${route.params.id}`);
        if (!res.ok) {
            throw new Error(`Debater request failed: ${res.status}`);
        }

        debater.value = await res.json();
    } catch (err) {
        console.error("Error fetching debater:", err);
        toast.error("تعذر تحميل بيانات المناظر.");
    }
};

const fetchAudienceReviews = async (page = 1) => {
    reviewsLoading.value = true;

    try {
        const headers = { Accept: "application/json" };
        if (auth.state.token) {
            headers.Authorization = `Bearer ${auth.state.token}`;
        }

        const res = await fetch(
            `/api/debaters/${route.params.id}/reviews?page=${page}`,
            { headers },
        );
        if (!res.ok) {
            throw new Error(`Audience reviews request failed: ${res.status}`);
        }

        const data = await res.json();
        if (!Array.isArray(data.data)) {
            throw new Error("Audience reviews response was not paginated.");
        }

        if (page > data.last_page && data.last_page > 0) {
            await fetchAudienceReviews(data.last_page);
            return;
        }

        audienceReviews.value = data.data;
        currentPage.value = data.current_page;
        totalReviews.value = data.total;
        ownReview.value = data.own_review;
        debater.value.audience_score = data.average_score;
    } catch (err) {
        console.error("Error fetching audience reviews:", err);
        toast.error("تعذر تحديث تقييمات الجمهور.");
    } finally {
        reviewsLoading.value = false;
    }
};

const submitAudienceRatingLegacy = async () => {
    const token = localStorage.getItem("token");

    if (!token || !auth.isAuthenticated.value) {
        toast.error("يرجى تسجيل الدخول أولاً لإرسال تقييمك.");
        return;
    }

    try {
        const res = await fetch(`/api/debaters/${route.params.id}/reviews`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify(ratingForm.value),
        });

        const data = await res.json();

        if (res.ok) {
            toast.success(data.message || "تم إرسال تقييمك بنجاح!");
            ratingForm.value = {
                reviewer_name: "",
                etiquette: 15,
                knowledge: 15,
                logic: 15,
                victory: 15,
                comment: "",
            };
            await fetchAudienceReviews();
        } else {
            toast.error(data.message || "حدث خطأ أثناء حفظ التقييم.");
        }
    } catch (err) {
        console.error("Error submitting audience review:", err);
        toast.error("تعذر الاتصال بالخادم.");
    }
};

const resetReviewForm = () => {
    ratingForm.value = {
        etiquette: 15,
        knowledge: 15,
        logic: 15,
        victory: 15,
        comment: "",
    };
    isEditingReview.value = false;
    editingReviewId.value = null;
};

const openCreateReview = () => {
    resetReviewForm();
    reviewDialogVisible.value = true;
};

const openEditReview = (review) => {
    isEditingReview.value = true;
    editingReviewId.value = review.id;
    ratingForm.value = {
        etiquette: review.etiquette,
        knowledge: review.knowledge,
        logic: review.logic,
        victory: review.victory,
        comment: review.comment || "",
    };
    reviewDialogVisible.value = true;
};

const saveAudienceReview = async () => {
    if (!auth.isAuthenticated.value || !auth.state.token) {
        toast.error("يرجى تسجيل الدخول أولاً لإرسال تقييمك.");
        return;
    }

    savingReview.value = true;

    try {
        const editing = isEditingReview.value;
        const url = editing
            ? `/api/audience-reviews/${editingReviewId.value}`
            : `/api/debaters/${route.params.id}/reviews`;
        const res = await fetch(url, {
            method: editing ? "PUT" : "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                Authorization: `Bearer ${auth.state.token}`,
            },
            body: JSON.stringify(ratingForm.value),
        });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || "حدث خطأ أثناء حفظ التقييم.");
        }

        reviewDialogVisible.value = false;
        resetReviewForm();
        toast.success(data.message || "تم حفظ تقييمك بنجاح!");
        await fetchAudienceReviews(editing ? currentPage.value : 1);
    } catch (err) {
        console.error("Error saving audience review:", err);
        toast.error(err.message || "تعذر الاتصال بالخادم.");
    } finally {
        savingReview.value = false;
    }
};

const confirmDeleteReview = (review) => {
    confirm.require({
        group: "audience-review",
        header: "حذف التقييم",
        message: "هل أنت متأكد من حذف تقييمك؟ لا يمكن التراجع عن هذا الإجراء.",
        acceptLabel: "حذف",
        rejectLabel: "إلغاء",
        acceptClass: "p-button-danger",
        accept: () => deleteAudienceReview(review),
    });
};

const deleteAudienceReview = async (review) => {
    try {
        const res = await fetch(`/api/audience-reviews/${review.id}`, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${auth.state.token}`,
            },
        });
        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.message || "تعذر حذف التقييم.");
        }

        toast.success(data.message || "تم حذف تقييمك بنجاح!");
        await fetchAudienceReviews(currentPage.value);
    } catch (err) {
        console.error("Error deleting audience review:", err);
        toast.error(err.message || "تعذر الاتصال بالخادم.");
    }
};

const onReviewsPage = (event) => {
    fetchAudienceReviews(event.page + 1);
};

const isCommentExpanded = (id) => expandedComments.value.has(id);

const toggleComment = (id) => {
    const updated = new Set(expandedComments.value);
    if (updated.has(id)) {
        updated.delete(id);
    } else {
        updated.add(id);
    }
    expandedComments.value = updated;
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString("ar-EG");
};

onMounted(async () => {
    await fetchDebater();
    if (debater.value) {
        await fetchAudienceReviews();
    }
});
</script>
