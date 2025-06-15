@extends('layouts.app')

@section('header_styles')
    @parent
    @vite('resources/js/public-calc.js')

<style>
.workflow {
    padding-top: 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 60px 90px;
    box-sizing: border-box;
    gap: 40px;
}

.workflow-description {
    flex: 0 0 360px;
    color: #fff;
    line-height: 1.7;
    font-size: 18px;
}

.workflow-bottom {
    padding-top: 0;
}

.calc-content {
    flex: 1;
    max-width: 820px;
}

.contact-form {
    margin-top: 60px;
}

.contact-form h2 {
    font-size: 34px;
    margin-bottom: 20px;
    color: #fff;
    text-align: center;
}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    background-color: #111;
    border: 1px solid #333;
    color: #fff;
    font-size: 16px;
    border-radius: 4px;
}

.contact-form button {
    width: 100%;
    background-color: #fff;
    color: #000;
    font-weight: 600;
    padding: 22px 32px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.contact-form label {
    font-size: 15px;
    color: #fff;
    display: block;
    margin-bottom: 4px;
}

.faq-block {
    max-width: 360px;
    margin-top: -166px;
    color: #fff;
}

.faq-block .workflow-title {
    font-size: 34px;
    margin-bottom: 20px;
}

.faq-question {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    margin-bottom: 8px;
}

.faq-question-icon {
    width: 18px;
    height: 18px;
    margin-right: 8px;
    fill: #fff;
}

.faq-answer {
    font-size: 15px;
    color: #aaa;
    line-height: 1.6;
    margin-bottom: 20px;
}

@media screen and (max-width: 1024px) {
    .workflow,
    .faq-block {
        flex-direction: column;
        padding: 30px 16px;
    }

    .workflow-description,
    .calc-content {
        max-width: 100%;
        flex: none;
    }
}
</style>
@endsection

@section('content')
    {{-- ВЕРХ: текст + калькулятор + форма --}}
    <div class="workflow">
        <div class="workflow-description">
            {!! $price->description1 !!}
        </div>

        <div class="calc-content">
            {{-- Vue3 калькулятор --}}
            <div id="calculator-app"></div>

            {{-- Contact Form --}}
            <div class="contact-form" style="margin-top: 60px;">
                <h2>Let's talk about your project</h2>
                <form>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 200px;">
                            <label>Name <span style="color: red">*</span></label>
                            <input type="text" name="name" required>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label>E-mail <span style="color: red">*</span></label>
                            <input type="email" name="email" required>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label>Phone number</label>
                            <input type="text" name="phone">
                        </div>
                    </div>

                    <label>Message <span style="color: red">*</span></label>
                    <textarea name="message" rows="6" required></textarea>

                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>

    {{-- НИЖЕ ФОРМЫ: FAQ слева (в том же стиле) --}}
    <div class="workflow" style="margin-top: 80px;">
        <div class="workflow-description">
            <div class="faq-block">
                <h2 class="workflow-title">Questions</h2>
                <div style="padding: 0">
                    {!! $price->description2 !!}
                </div>
                @foreach ($faq_list as $item)
                    <div class="faq-item">
                        <div class="faq-question js-question">
                            <svg class="faq-question-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21.2 11.6">
                                <path d="M10.6 9.6L20.2 0l1 1-10.6 10.6L0 1l1-1 9.6 9.6z"/>
                            </svg>
                            {!! $item->question !!}
                        </div>
                        <div class="faq-answer js-answer">{!! $item->answer !!}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Пустой div для выравнивания правой стороны --}}
        <div class="calc-content"></div>
    </div>
@endsection


@section('scripts')
    <script>
        window.calcData = {!! json_encode($price, JSON_UNESCAPED_UNICODE) !!};
        window.priceStr = {!! json_encode($priceStr, JSON_UNESCAPED_UNICODE) !!};
        window.coefficientData = {!! json_encode($coefficientData, JSON_UNESCAPED_UNICODE) !!};
    </script>

    <script>
        window.addEventListener('load', () => {
            document.querySelector('.loader')?.remove();
            document.body?.classList.remove('loading');
        });
    </script>
    @parent
@endsection
