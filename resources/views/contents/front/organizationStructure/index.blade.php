@extends('layouts.front.theme')

@section('title', 'الهيكل التنظيمى - الهيئة العامة للتنمية الصناعية')

@section('content')
<!-- Content -->
<section id="content">
    <div class="content-wrap">
        <style type="text/css">
            .toggle-icon .toggle-open {
                display: none !important;   /* plus hidden by default */
            }
            .toggle-icon .toggle-closed {
                display: inline-block !important; /* minus shown by default */
            }

            /* Toggle-active (your markup currently marks closed items with this class) */
            .toggle.toggle-active .toggle-open {
                display: inline-block !important; /* show plus when closed */
            }
            .toggle.toggle-active .toggle-closed {
                display: none !important; /* hide minus when closed */
            }
        </style>
        
        <div class="container">
            <div class="justify-content-center">
                <a data-lightbox="image" href="/front/img/structure01.jpg">
                    <img alt=" الهيكل التنظيمى" src="/front/img/structure01.jpg" style="max-width: 70vw; margin-top: 5rem;" />
                </a>
            </div>

            <div class="row col-mb-50 mb-0 d-flex justify-content-center align-items-center flex-column">
                <!-- Right Column - رئيس الهيئة -->
                <div class="col-md-6">
                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                        <div class="toggle-header" style="background-color: var(--bs-gray-900); color:#fff;">
                            <div class="toggle-icon">
                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                            </div>
                            <div class="toggle-title txt-white">رئيس الهيئة</div>
                        </div>

                        <div class="toggle-content toggle-content-padding" style="display: none;">
                            <div class="row">
                                <div class="col-md-12 ps-4 ms-3 bg-white">
                                    <!-- الإدارة المركزية لشئون مكتب رئيس الهيئة -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية لشئون مكتب رئيس الهيئة
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-6 رئيس الإدارة المركزية لشئون مكتب رئيس الهيئة.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-17 مدير عام الإدارة العامة للسكرتارية والمكتب الفنى.pdf" target="_blank">الإدارة العامة للسكرتارية والمكتب الفنى</a></p>
                                            <p>&nbsp;</p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-18 مدير عام الإدارة العامة للعلاقات العامة والمراسم والإعلام.pdf" target="_blank">الإدارة العامة للعلاقات العامة والمراسم والإعلام</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-19 مدير عام الإدارة العامة للإتصال السياسى والعلاقات الحكومية.pdf" target="_blank">الإدارة العامة للإتصال السياسى والعلاقات الحكومية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-20 مدير عام الإدارة العامة للعلاقات الدولية والإتفاقيات.pdf" target="_blank">الإدارة العامة للعلاقات الدولية والاتفاقيات</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-21 مدير عام الإدارة العامة للتسويق والترويج.pdf" target="_blank">الإدارة العامة للتسويق والترويج</a></p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية لنظم المعلومات والتحول الرقمي -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية لنظم المعلومات والتحول الرقمي
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-7 رئيس الإدارة المركزية لنظم المعلومات والتحول الرقمى.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-22 مدير عام الإدارة العامة للإحصاء والتقارير والنشر الإلكترونى.pdf" target="_blank">الإدارة العامة للإحصاء والتقارير والنشر الإلكتروني</a></p>
                                            <p>&nbsp;</p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-23 مدير عام الإدارة العامة للنظم والتطبيقات والدعم الفنى.pdf" target="_blank">الإدارة العامة للنظم والتطبيقات والدعم الفني</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-24 مدير عام الإدارة العامة للبنية الأساسية وتأمين المعلومات.pdf" target="_blank">الإدارة العامة للبنية الأساسية وتأمين المعلومات</a></p>
                                            <p>&nbsp;</p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية لأقاليم الهيئة -->
                                    <div class="col-md-12">
                                        <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                            <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                                <div class="toggle-icon">
                                                    <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                    <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                                </div>
                                                <div class="toggle-title">
                                                    الإدارة المركزية لأقاليم الهيئة
                                                    <span class="small" style="font-weight: normal;">
                                                        (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                        <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/رئيس مركزية الاقاليم.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام التنسيق والتطوير.pdf.pdf" target="_blank">الإدارة العامة لتنسيق وتطوير شئون الفروع</a></p>
                                                
                                                <!-- إقليم القاهرة الكبرى -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed fas fa-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم القاهرة الكبرى
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم القاهرة الكبرى.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع شرق القاهرة.pdf" target="_blank">فرع شرق القاهرة</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع وسط القاهرة.pdf" target="_blank">فرع وسط القاهرة</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع6 أكتوبر.pdf" target="_blank">فرع 6 أكتوبر</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع القليوبية.pdf" target="_blank">فرع القليوبية</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم الدلتا -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed fas fa-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم الدلتا
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم الدلتا.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع المحلة.pdf" target="_blank">فرع المحلة</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع السادات.pdf" target="_blank">فرع السادات</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع قويسنا.pdf" target="_blank">فرع قويسنا</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم الأسكندرية -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed fas fa-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم الأسكندرية
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم الاسكندرية.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع البحيرة.pdf" target="_blank">فرع البحيرة</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع كفر الشيخ.pdf" target="_blank">فرع كفر الشيخ</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم القناة -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed uil uil-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم القناة
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم القناة.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع العاشر.pdf" target="_blank">فرع العاشر</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع بدر.pdf" target="_blank">فرع بدر</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع بورسعيد.pdf" target="_blank">فرع بورسعيد</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع الزقازيق.pdf" target="_blank">فرع الزقازيق</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم شمال الصعيد -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed uil uil-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم شمال الصعيد
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="#" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع المنيا.pdf" target="_blank">فرع المنيا</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم وسط الصعيد -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed uil uil-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم وسط الصعيد
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم وسط الصعيد.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع الوادى الجديدا.pdf" target="_blank">فرع الوادي الجديد</a></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- إقليم جنوب الصعيد -->
                                                <div class="col-md-12">
                                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                                        <div class="toggle-header" style="background-color: var(--bs-gray-300);">
                                                            <div class="toggle-icon">
                                                                <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                                <i class="toggle-closed uil uil-minus-circle" style="display: none;">&nbsp;</i>
                                                            </div>
                                                            <div class="toggle-title">
                                                                الإدارة العامة لإقليم جنوب الصعيد
                                                                <span class="small" style="font-weight: normal;">
                                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                                    <a href="/uploads/files/pdfs/مهام مركزية الاقاليم/مدير عام الادارة العامة لاقليم جنوب الصعيد.pdf" target="_blank">فتح الملف</a>)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع الاقصر.pdf" target="_blank">فرع الأقصر</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع قنا.pdf" target="_blank">فرع قنا</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع أسوان.pdf" target="_blank">فرع أسوان</a></p>
                                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الفروع/مدير فرع البحر الاحمر.pdf" target="_blank">فرع البحر الأحمر</a></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية للشئون المالية والإدارية -->
                                    <div class="col-md-12">
                                        <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                            <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                                <div class="toggle-icon">
                                                    <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                    <i class="toggle-closed uil uil-minus-circle">&nbsp;</i>
                                                </div>
                                                <div class="toggle-title">
                                                    الإدارة المركزية للشئون المالية والإدارية
                                                    <span class="small" style="font-weight: normal;">
                                                        (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                        <a href="/uploads/files/pdfs/مهام المالية والادارية/رئيس الادارة المركزية للشئون المالية و الادارية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام المالية والادارية/مدير عام الشئون المالية.pdf" target="_blank">الإدارة العامة للشئون المالية</a></p>
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام المالية والادارية/مدير عام الإدارة العامة للتعاقدات والمخازن.pdf" target="_blank">الإدارة العامة للتعاقدات والمخازن</a></p>
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام المالية والادارية/مدير عام الشئون االادارية.pdf" target="_blank">الإدارة العامة للشئون الإدارية</a></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية للموارد البشرية -->
                                    <div class="col-md-12">
                                        <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                            <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                                <div class="toggle-icon">
                                                    <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                    <i class="toggle-closed uil uil-minus-circle">&nbsp;</i>
                                                </div>
                                                <div class="toggle-title">
                                                    الإدارة المركزية للموارد البشرية
                                                    <span class="small" style="font-weight: normal;">
                                                        (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                        <a href="/uploads/files/pdfs/مركزية الموارد البشرية/وظيفة رئيس الادارة المركزية للموارد البشرية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="toggle-content ps-4 ms-3" style="display: none;">
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مركزية الموارد البشرية/مدير عام التطوير المؤسسي.pdf" target="_blank">الإدارة العامة للتطوير المؤسسي</a></p>
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مركزية الموارد البشرية/وظيفة مدير عام عمليات الموارد البشرية.pdf" target="_blank">الإدارة العامة لعمليات الموارد البشرية</a></p>
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مركزية الموارد البشرية/وظيفة مدير عام تنمية المواهب.pdf" target="_blank">الإدارة العامة لإدارة وتنمية المواهب</a></p>
                                                <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مركزية الموارد البشرية/وظيفة مدير عام الاستحقاقات و المزايا.pdf" target="_blank">الإدارة العامة للإستحقاقات والمزايا</a></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- الإدارة العامة للإدارة الاستراتيجية -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open uil uil-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed uil uil-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة العامة للإدارة الاستراتيجية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-28 مدير عام الإدارة العامة للإدارة الإستراتيجية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/192-1 مدير إدارة التخطيط الاستراتيجى والسياسات.pdf" target="_blank">إدارة التخطيط الاستراتيجي والسياسات</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/192-2 مدير إدارة المتابعة والتقييم.pdf" target="_blank">إدارة المتابعة والتقييم</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/192-3 مدير إدارة المشروعات.pdf" target="_blank">إدارة المشروعات</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/192-4 مدير إدارة الأزمات والكوارث والحد من المخاطر.pdf" target="_blank">إدارة الأزمات والكوارث والحد من المخاطر</a></p>
                                        </div>
                                    </div>

                                    <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i><a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-25 مدير عام الإدارة العامة للإدارة القانونية.pdf" target="_blank">الإدارة العامة للإدارة القانونية</a></p>
                                    <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i><a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-26 مدير عام الإدارة العامة للأمن.pdf" target="_blank">الإدارة العامة للأمن</a></p>
                                    <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i><a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-27 مدير عام الإدارة العامة للمراجعة الداخلية والحوكمة.pdf" target="_blank">الإدارة العامة للمراجعة الداخلية والحوكمة</a></p>
                                    <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i><a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-29 مدير عام الإدارة العامة لخدمة العملاء.pdf" target="_blank">الإدارة العامة لخدمة العملاء</a></p>
                                    <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i><a href="/uploads/files/pdfs/مهام قطاع رئيس الهيئة/190-30 مدير عام الإدارة العامة لدراسات الجدوى الاقتصادية.pdf" target="_blank">الإدارة العامة لدراسات الجدوى الاقتصادية</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Left Column - نائب رئيس الهيئة للخدمات وتنمية الصناعة -->
                <div class="col-md-6 ps-4">
                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                        <div class="toggle-header" style="background-color: var(--bs-gray-600); color:#fff;">
                            <div class="toggle-icon">
                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                            </div>
                            <div class="toggle-title">نائب رئيس الهيئة للخدمات وتنمية الصناعة</div>
                        </div>

                        <div class="toggle-content toggle-content-padding" style="display: none;">
                            <div class="row">
                                <div class="col-md-12 ps-4 bg-white">
                                    <!-- الإدارة المركزية لتنمية وتطوير الصناعة المحلية -->
                                    <div class="toggle mb-0 pt-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية لتنمية وتطوير الصناعة المحلية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/رئيس الإدارة المركزية لتنمية وتطوير الصناعة المحلية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة لدراسة تطوير الصناعة المحلية.pdf" target="_blank">الإدارة العامة لدراسات تطوير التصنيع المحلي</a></p>
                                            <p>&nbsp;</p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة لخدمات المكون%20 المحلى.pdf" target="_blank">الإدارة العامة لخدمات المكون المحلي</a></p>
                                            <p>&nbsp;</p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية للشئون الفنية -->
                                    <div class="toggle mb-0 pt-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية للشئون الفنية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/رئيس الإدارة المركزية للشئون الفنية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة لصناعة الغزل والنسيج والملابس الجاهزة.pdf" target="_blank">الإدارة العامة لصناعة الغزل والنسيج والملابس الجاهزة</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات والمستلزمات الطبية.pdf" target="_blank">الإدارة العامة للصناعات والمستلزمات الطبية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات التعدينية ومواد البناء والحراريات.pdf" target="_blank">الإدارة العامة للصناعات التعدينية ومواد البناء والحراريات</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات الهندسية.pdf" target="_blank">الإدارة العامة للصناعات الهندسية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات الكهربائية والإلكترونية.pdf" target="_blank">الإدارة العامة للصناعات الكهربائية والإلكترونية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات والمخازن الكيماوية والجلود.pdf" target="_blank">الإدارة العامة للصناعات والمخازن الكيماوية والجلود</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات الغذائية.pdf" target="_blank">الإدارة العامة للصناعات الغذائية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات الخشبية والأثاث.pdf" target="_blank">الإدارة العامة للصناعات الخشبية والاثاث</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/الإدارة العامة للصناعات المعدنية.pdf" target="_blank">الإدارة العامة للصناعات المعدنية</a></p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية للسجل والتراخيص الصناعية -->
                                    <div class="toggle mb-0 pt-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية للسجل والتراخيص الصناعية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/190-11 رئيس الإدارة المركزية للسجل والتراخيص الصناعية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/190-44 مدير عام الإدارة العامة للسجل والموافقات الصناعية.pdf" target="_blank">الإدارة العامة للسجل والموافقات الصناعية</a></p>
                                            <p>&nbsp;</p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/190-45 مدير عام الإدارة العامة لتراخيص البيئية.pdf" target="_blank">الإدارة العامة لتراخيص البيئة</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/الخدمات وتنمية الصناعة/م.ع السلامة والصحة المهنية.pdf" target="_blank">الإدارة العامة لتراخيص الدفاع المدنى والسلامة والصحة المهنية</a></p>
                                            <p>&nbsp;</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Left Column 2 - نائب رئيس الهيئة لتنمية وإدارة المناطق الصناعية -->
                <div class="col-md-6 ps-4">
                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                        <div class="toggle-header" style="background-color: var(--bs-gray-600); color:#fff;">
                            <div class="toggle-icon">
                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                            </div>
                            <div class="toggle-title">نائب رئيس الهيئة لتنمية وإدارة المناطق الصناعية</div>
                        </div>

                        <div class="toggle-content toggle-content-padding" style="display: none;">
                            <div class="row">
                                <div class="col-md-12 ps-4 bg-white">
                                    <!-- الإدارة المركزية للمشروعات الصناعية -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية للمشروعات الصناعية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/رئيس الإدارة المركزية للمشروعات الصناعية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة لمتابعة تنفيذ المشروعات الصناعية.pdf" target="_blank">الإدارة العامة لمتابعة تنفيذ المشروعات الصناعية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة للبنية التحتية والمرافق.pdf" target="_blank">الإدارة العامة للبنية التحتية والمرافق</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة لدراسات وتخطيط المشروعات الصناعية.pdf" target="_blank">الإدارة العامة لدراسات وتخطيط المشروعات الصناعية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/عام الإدارة العامة للمقايسات والمراجعة.pdf" target="_blank">الإدارة العامة للمقايسات والمراجعة</a></p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية لتخصيص ومتابعة الأراضي والوحدات الصناعية -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية لتخصيص ومتابعة الأراضي والوحدات الصناعية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/مهام تخصيص ومتابعة الاراضي/رئيس الإدارة المركزية لتخصيص ومتابعة الأراضي والوحدات الصناعية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام تخصيص ومتابعة الاراضي/مدير عام الإدارة العامة لتخصيص ومتابعة الأراضي.pdf" target="_blank">الإدارة العامة لتخصيص ومتابعة الأراضي</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام تخصيص ومتابعة الاراضي/مدير عام الإدارة العامة لتخصيص ومتابعة الوحدات الصناعية.pdf" target="_blank">الإدارة العامة لتخصيص ومتابعة الوحدات الصناعية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام تخصيص ومتابعة الاراضي/مدير عام الإدارة العامة لرخص البناء.pdf" target="_blank">الإدارة العامة لرخص البناء</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/مهام تخصيص ومتابعة الاراضي/مدير عام الإدارة العامة للتصرفات العقارية.pdf" target="_blank">الإدارة العامة للتصرفات العقارية</a></p>
                                        </div>
                                    </div>

                                    <!-- الإدارة المركزية للمناطق الصناعية -->
                                    <div class="toggle mb-0 pt-3 ps-3 toggle-active">
                                        <div class="toggle-header" style="background-color: var(--bs-gray-500);">
                                            <div class="toggle-icon">
                                                <i class="toggle-open fas fa-plus-circle">&nbsp;</i>
                                                <i class="toggle-closed fas fa-minus-circle">&nbsp;</i>
                                            </div>
                                            <div class="toggle-title">
                                                الإدارة المركزية للمناطق الصناعية
                                                <span class="small" style="font-weight: normal;">
                                                    (<i class="fa-solid fa-file-pdf">&nbsp;</i>
                                                    <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/رئيس الإدارة المركزية للمناطق الصناعية.pdf" style="color: #fff !important;" target="_blank">فتح الملف</a>)
                                                </span>
                                            </div>
                                        </div>
                                        <div class="toggle-content ps-4 ms-3" style="display: none;">
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة للمناطق الصناعية بالمحافظات والمدن الجديدة.pdf" target="_blank">الإدارة العامة للمناطق الصناعية بالمحافظات والمدن الجديدة</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة لدراسات وتخطيط المناطق الصناعية.pdf" target="_blank">الإدارة العامة لدراسة وتخطيط المناطق الصناعية</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية" target="_blank">الإدارة العامة للخرائط</a></p>
                                            <p><i class="fa-solid fa-file-pdf text-danger">&nbsp;</i> <a href="/uploads/files/pdfs/تنمية وادارة المناطق الصناعية/مدير عام الإدارة العامة للمطور الصناعي.pdf" target="_blank">الإدارة العامة للمطور الصناعى</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديد جميع عناصر الهيدر القابلة للنقر
    const toggleHeaders = document.querySelectorAll('.toggle-header');

    toggleHeaders.forEach(header => {
        header.addEventListener('click', function() {
            // العثور على العنصر الأب (التoggle الرئيسي) والمحتوى والأيقونات
            const parentToggle = this.closest('.toggle');
            const content = parentToggle.querySelector('.toggle-content');
            const openIcon = parentToggle.querySelector('.toggle-open');
            const closedIcon = parentToggle.querySelector('.toggle-closed');
            
            // التبديل بين إظهار وإخفاء المحتوى
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                if(openIcon) openIcon.style.display = 'none';
                if(closedIcon) closedIcon.style.display = 'inline-block';
                parentToggle.classList.add('toggle-active');
            } else {
                content.style.display = 'none';
                if(openIcon) openIcon.style.display = 'inline-block';
                if(closedIcon) closedIcon.style.display = 'none';
                parentToggle.classList.remove('toggle-active');
            }
        });
        
        // تغيير شكل المؤشر عند المرور ليدل على إمكانية النقر
        header.style.cursor = 'pointer';
    });
});
</script>
@endsection
