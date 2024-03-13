컨트롤러와 모델관계
    컨트롤러 => 모델 => 데이터베이스
    CategoryController => Category => categories
    CountryController => Country => countries
    EpriceController => Eprice => eprices
    MemberController => User => users
    PostController => Post => posts

주요 methods 역할
    index: 모델안에 모든 데이터 보기
    show: 한 데이터만 보기
    store: 저장
    update: 업데이트
    destroy: 삭제

Resource 이름 의미
    //콘트롤러collection: 다보여주는거
    //콘트롤러resource: 하나만 보여줌 (대신 foreign key에 연결된 모델까지 다 보여줌)
